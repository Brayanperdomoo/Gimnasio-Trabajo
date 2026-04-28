<?php
namespace App\Services;

use App\Repositories\EntrenadorRepository;

final class EntrenadorService extends BaseService
{
    public function __construct(EntrenadorRepository $repository)
    {
        parent::__construct($repository, ['nombre', 'especialidad', 'email']);
    }
}
