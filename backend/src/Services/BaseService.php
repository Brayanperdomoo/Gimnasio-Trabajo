<?php
namespace App\Services;

use App\Repositories\BaseRepository;
use InvalidArgumentException;

abstract class BaseService
{
    public function __construct(protected BaseRepository $repository, protected array $required = []) {}

    public function list(): array { return $this->repository->all(); }
    public function get(int $id): ?array { return $this->repository->find($id); }
    public function filter(array $query): array { return $this->repository->filter($query); }

    public function create(array $data): array
    {
        $this->validateRequired($data);
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): ?array
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    protected function validateRequired(array $data): void
    {
        foreach ($this->required as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === '' || $data[$field] === null) {
                throw new InvalidArgumentException("El campo {$field} es obligatorio");
            }
        }
    }
}
