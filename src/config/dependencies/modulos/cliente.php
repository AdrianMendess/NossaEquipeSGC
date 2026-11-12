<?php

declare(strict_types=1);

use Src\App\Http\Controllers\ClienteController;
use Src\App\Infrastructure\IRepositories\IClienteRepository;
use Src\App\Infrastructure\Repositories\ClienteRepository;
use Src\App\Services\Auditoria\AuditoriaCliente;
use Src\App\Services\ClienteService;
use Src\App\Services\IServices\IAuditoriaService;
use Src\App\Services\IServices\IClienteService;
use Src\Core\Container;
use Src\Core\Database;


// =====================
// REPOSITORIES
// =====================
Container::set(IClienteRepository::class, static function () {
    return new AuditoriaCliente(
        new ClienteRepository(Container::get(Database::class)),
        Container::get(IAuditoriaService::class)
    );
});

// =====================
// SERVICES
// =====================
Container::set(IClienteService::class, static function () {
    return new ClienteService(
        Container::get(IClienteRepository::class)
    );
});

// =====================
// CONTROLLERS
// =====================
Container::set(ClienteController::class, static function () {
    return new ClienteController(
        Container::get(IClienteService::class)
    );
});
