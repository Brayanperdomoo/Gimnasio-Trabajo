<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\BaseService;
use InvalidArgumentException;
use Throwable;

abstract class BaseController
{
    public function __construct(protected BaseService $service) {}

    public function index(): void { Response::json($this->service->list()); }

    public function show(int $id): void
    {
        $row = $this->service->get($id);
        $row ? Response::json($row) : Response::json(['error' => 'Registro no encontrado'], 404);
    }

    public function filter(): void { Response::json($this->service->filter(Request::query())); }

    public function store(): void
    {
        try {
            Response::json($this->service->create(Request::body()), 201);
        } catch (InvalidArgumentException $e) {
            Response::json(['error' => $e->getMessage()], 422);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudo crear el registro', 'detail' => $e->getMessage()], 400);
        }
    }

    public function update(int $id): void
    {
        try {
            $row = $this->service->update($id, Request::body());
            $row ? Response::json($row) : Response::json(['error' => 'Registro no encontrado'], 404);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudo actualizar el registro', 'detail' => $e->getMessage()], 400);
        }
    }

    public function destroy(int $id): void
    {
        try {
            $this->service->delete($id)
                ? Response::json(['message' => 'Registro eliminado correctamente'])
                : Response::json(['error' => 'Registro no encontrado'], 404);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudo eliminar el registro', 'detail' => $e->getMessage()], 400);
        }
    }
}
