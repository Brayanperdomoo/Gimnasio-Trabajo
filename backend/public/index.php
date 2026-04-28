<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Core\Response;
use App\Repositories\EntrenadorRepository;
use App\Repositories\PlanRepository;
use App\Repositories\MiembroRepository;
use App\Repositories\ClaseRepository;
use App\Repositories\ReservaRepository;
use App\Services\EntrenadorService;
use App\Services\PlanService;
use App\Services\MiembroService;
use App\Services\ClaseService;
use App\Services\ReservaService;
use App\Controllers\EntrenadorController;
use App\Controllers\PlanController;
use App\Controllers\MiembroController;
use App\Controllers\ClaseController;
use App\Controllers\ReservaController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$method = $_SERVER['REQUEST_METHOD'];

if ($path === '/' || $path === '/health') {
    Response::json(['status' => 'ok', 'service' => 'gimnasio-backend']);
    exit;
}
if ($path === '/docs') {
    header('Content-Type: text/html; charset=utf-8');
    echo file_get_contents(__DIR__ . '/../docs/swagger-ui.html');
    exit;
}
if ($path === '/openapi.json') {
    header('Content-Type: application/json; charset=utf-8');
    echo file_get_contents(__DIR__ . '/../docs/openapi.json');
    exit;
}

$db = Database::getConnection();
$controllers = [
    'entrenadores' => new EntrenadorController(new EntrenadorService(new EntrenadorRepository($db))),
    'planes' => new PlanController(new PlanService(new PlanRepository($db))),
    'miembros' => new MiembroController(new MiembroService(new MiembroRepository($db))),
    'clases' => new ClaseController(new ClaseService(new ClaseRepository($db))),
    'reservas' => new ReservaController(new ReservaService(new ReservaRepository($db))),
];

if (preg_match('#^/api/([a-z_]+)(?:/(filter|\d+))?$#', $path, $matches)) {
    $resource = $matches[1];
    $argument = $matches[2] ?? null;
    if (!isset($controllers[$resource])) {
        Response::json(['error' => 'Recurso no existe'], 404); exit;
    }
    $controller = $controllers[$resource];
    if ($argument === 'filter' && $method === 'GET') { $controller->filter(); exit; }
    if ($argument !== null && ctype_digit($argument)) {
        $id = (int)$argument;
        match ($method) {
            'GET' => $controller->show($id),
            'PUT' => $controller->update($id),
            'DELETE' => $controller->destroy($id),
            default => Response::json(['error' => 'Metodo no permitido'], 405),
        };
        exit;
    }
    if ($argument === null) {
        match ($method) {
            'GET' => $controller->index(),
            'POST' => $controller->store(),
            default => Response::json(['error' => 'Metodo no permitido'], 405),
        };
        exit;
    }
}
Response::json(['error' => 'Ruta no encontrada'], 404);
