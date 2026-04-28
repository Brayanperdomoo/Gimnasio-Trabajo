<?php
namespace App\Repositories;

use PDO;

abstract class BaseRepository
{
    public function __construct(
        protected PDO $db,
        protected string $table,
        protected array $fillable,
        protected array $filterable
    ) {}

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): array
    {
        $clean = $this->onlyFillable($data);
        $columns = array_keys($clean);
        $params = array_map(fn($c) => ':' . $c, $columns);
        $sql = "INSERT INTO {$this->table} (" . implode(',', $columns) . ") VALUES (" . implode(',', $params) . ") RETURNING *";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($clean);
        return $stmt->fetch();
    }

    public function update(int $id, array $data): ?array
    {
        if (!$this->find($id)) {
            return null;
        }
        $clean = $this->onlyFillable($data);
        if (!$clean) {
            return $this->find($id);
        }
        $sets = array_map(fn($c) => "{$c} = :{$c}", array_keys($clean));
        if (in_array('updated_at', $this->columns(), true)) {
            $sets[] = "updated_at = CURRENT_TIMESTAMP";
        }
        $clean['id'] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE id = :id RETURNING *";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($clean);
        return $stmt->fetch();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function filter(array $query): array
    {
        $conditions = [];
        $params = [];
        foreach ($this->filterable as $field) {
            if (isset($query[$field]) && $query[$field] !== '') {
                $param = str_replace('.', '_', $field);
                $conditions[] = "CAST({$field} AS TEXT) ILIKE :{$param}";
                $params[$param] = '%' . $query[$field] . '%';
            }
        }
        if (isset($query['search']) && $query['search'] !== '') {
            $searchConditions = [];
            foreach ($this->filterable as $index => $field) {
                $param = 'search_' . $index;
                $searchConditions[] = "CAST({$field} AS TEXT) ILIKE :{$param}";
                $params[$param] = '%' . $query['search'] . '%';
            }
            if ($searchConditions) {
                $conditions[] = '(' . implode(' OR ', $searchConditions) . ')';
            }
        }
        $sql = "SELECT * FROM {$this->table}";
        if ($conditions) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        $sql .= " ORDER BY id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    protected function onlyFillable(array $data): array
    {
        return array_intersect_key($data, array_flip($this->fillable));
    }

    protected function columns(): array
    {
        return array_merge(['id'], $this->fillable, ['created_at', 'updated_at']);
    }
}
