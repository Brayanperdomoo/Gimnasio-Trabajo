<?php
namespace App\Services;

use App\Repositories\ReservaRepository;

final class ReservaService extends BaseService
{
    public function __construct(ReservaRepository $repository)
    {
        parent::__construct($repository, ['miembro_id', 'clase_id']);
    }
}
