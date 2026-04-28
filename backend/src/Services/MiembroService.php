<?php
namespace App\Services;

use App\Repositories\MiembroRepository;

final class MiembroService extends BaseService
{
    public function __construct(MiembroRepository $repository)
    {
        parent::__construct($repository, ['plan_id', 'nombre', 'email']);
    }
}
