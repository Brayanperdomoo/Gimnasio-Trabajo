<?php
namespace App\Repositories;

use PDO;

final class PlanRepository extends BaseRepository
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'planes', ['nombre', 'descripcion', 'precio_mensual', 'duracion_dias', 'activo'], ['nombre', 'descripcion']);
    }
}
