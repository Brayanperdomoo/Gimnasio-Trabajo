<?php
namespace App\Repositories;

use PDO;

final class MiembroRepository extends BaseRepository
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'miembros', ['plan_id', 'nombre', 'email', 'telefono', 'fecha_inscripcion', 'estado'], ['nombre', 'email', 'estado']);
    }
}
