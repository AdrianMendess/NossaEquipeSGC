<?php

declare(strict_types=1);

use Src\App\Http\Controllers\EquipeController;
use Src\App\Infrastructure\IRepositories\IEquipeRepository;
use Src\App\Infrastructure\Repositories\EquipeRepository;
use Src\App\Services\Auditoria\AuditoriaEquipe;
use Src\App\Services\EquipeService;
use Src\App\Services\IServices\IAuditoriaService;
use Src\App\Services\IServices\IEquipeService;
use Src\Core\Container;
use Src\Core\Database;


// =====================
// REPOSITORIES
// =====================
Container::set(IEquipeRepository::class, static function () {
    return new AuditoriaEquipe(
        new EquipeRepository(Container::get(Database::class)),
        Container::get(IAuditoriaService::class)
    );
});

// =====================
// SERVICES
// =====================
Container::set(IEquipeService::class, static function () {
    return new EquipeService(
        Container::get(IEquipeRepository::class)
    );
});

// =====================
// CONTROLLERS
// =====================
Container::set(EquipeController::class, static function () {
    return new EquipeController(
        Container::get(IEquipeService::class)
    );
});
