<?php
namespace App\Repositories;

use PDO;

final class EntrenadorRepository extends BaseRepository
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'entrenadores', ['nombre', 'especialidad', 'email', 'telefono', 'activo'], ['nombre', 'especialidad', 'email']);
    }
}
