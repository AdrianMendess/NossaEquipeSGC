<?php

declare(strict_types=1);

use Src\App\Http\Middleware\AdminMiddleware;
use Src\App\Http\Middleware\AuthMiddleware;
use Src\App\Http\Middleware\GuestMiddleware;
use Src\App\Http\Middleware\LoginRateLimitingMiddleware;
use Src\App\Infrastructure\HttpEmailSender;
use Src\App\Infrastructure\IEmailSender;
use Src\App\Infrastructure\IRepositories\IAuditoriaRepository;
use Src\App\Infrastructure\Repositories\AuditoriaRepository;
use Src\App\Services\AuditoriaService;
use Src\App\Services\EmailService;
use Src\App\Services\IServices\IAuditoriaService;
use Src\App\Services\IServices\IAuthService;
use Src\App\Services\IServices\IEmailService;
use Src\App\Services\IServices\IUserService;
use Src\Core\Container;
use Src\Core\Database;
use Src\Core\RateLimiting;

Container::set(Database::class, static function () {
    return new Database();
});

Container::set(AuthMiddleware::class, static function () {
    return new AuthMiddleware(Container::get(IAuthService::class));
});

Container::set(AdminMiddleware::class, static function () {
    return new AdminMiddleware(Container::get(IAuthService::class));
});

Container::set(GuestMiddleware::class, static function () {
    return new GuestMiddleware(Container::get(IAuthService::class));
});

Container::set(LoginRateLimitingMiddleware::class, static function () {
    return new LoginRateLimitingMiddleware(Container::get(RateLimiting::class));
});

Container::set(IEmailSender::class, static function () {
    return new HttpEmailSender();
});

Container::set(IEmailService::class, static function () {
    return new EmailService(Container::get(IUserService::class), Container::get(IEmailSender::class));
});

Container::set(IAuditoriaRepository::class, static function () {
    return new AuditoriaRepository(Container::get(Database::class));
});

Container::set(IAuditoriaService::class, static function () {
    return new AuditoriaService(Container::get(IAuditoriaRepository::class));
});

