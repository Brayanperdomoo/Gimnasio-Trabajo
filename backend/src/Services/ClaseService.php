<?php
namespace App\Services;

use App\Repositories\ClaseRepository;

final class ClaseService extends BaseService
{
    public function __construct(ClaseRepository $repository)
    {
        parent::__construct($repository, ['entrenador_id', 'nombre', 'cupo_maximo', 'horario']);
    }
}
