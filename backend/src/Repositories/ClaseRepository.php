<?php
namespace App\Repositories;

use PDO;

final class ClaseRepository extends BaseRepository
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'clases', ['entrenador_id', 'nombre', 'descripcion', 'cupo_maximo', 'horario', 'activo'], ['nombre', 'descripcion']);
    }
}
