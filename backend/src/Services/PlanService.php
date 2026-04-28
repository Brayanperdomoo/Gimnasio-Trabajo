<?php
namespace App\Services;

use App\Repositories\PlanRepository;

final class PlanService extends BaseService
{
    public function __construct(PlanRepository $repository)
    {
        parent::__construct($repository, ['nombre', 'precio_mensual', 'duracion_dias']);
    }
}
