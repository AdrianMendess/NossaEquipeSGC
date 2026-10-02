<?php

declare(strict_types=1);

use Src\App\Http\Controllers\LoginController;
use Src\App\Infrastructure\IEmailSender;
use Src\App\Infrastructure\IRepositories\IUserRepository;
use Src\App\Infrastructure\Repositories\UserRepository;
use Src\App\Services\Auditoria\AuditoriaUser;
use Src\App\Services\AuthService;
use Src\App\Services\IServices\IAuditoriaService;
use Src\App\Services\IServices\IAuthService;
use Src\App\Services\IServices\IRecuperacaoSenhaService;
use Src\App\Services\IServices\IUserService;
use Src\App\Services\RecuperacaoSenhaService;
use Src\App\Services\UserService;
use Src\Core\Container;
use Src\Core\Database;

// Repositories
// Services
// Controllers

// =====================
// REPOSITORIES
// =====================
Container::set(IUserRepository::class, static function () {
    return new AuditoriaUser(
        new UserRepository(Container::get(Database::class)),
        Container::get(IAuditoriaService::class)
    );
});

// =====================
// SERVICES
// =====================
Container::set(IAuthService::class, static function () {
    return new AuthService(
        Container::get(IUserRepository::class),
    );
});

Container::set(IUserService::class, static function () {
    return new UserService(
        Container::get(IUserRepository::class),
    );
});

Container::set(IRecuperacaoSenhaService::class, static function () {
    return new RecuperacaoSenhaService(
        Container::get(IUserRepository::class),
        Container::get(IEmailSender::class),
    );
});

// =====================
// CONTROLLERS
// =====================
Container::set(LoginController::class, static function () {
    return new LoginController(
        Container::get(IAuthService::class),
        Container::get(IUserService::class),
        Container::get(IRecuperacaoSenhaService::class),
    );
});
