<?php
namespace App\Repositories;

use PDO;

final class ReservaRepository extends BaseRepository
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'reservas', ['miembro_id', 'clase_id', 'fecha_reserva', 'estado'], ['miembro_id', 'clase_id', 'estado']);
    }
}
