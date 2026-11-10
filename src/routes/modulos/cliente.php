<?php

declare(strict_types=1);

use Src\App\Http\Controllers\ClienteController;
use Src\App\Http\Middleware\AuthMiddleware;

// TODO: renomeie as rotas para o seu domínio (ex: /produtos, /clientes)
$router->get('/cliente', [ClienteController::class, 'index'], [AuthMiddleware::class]);
$router->get('/cliente/criar', [ClienteController::class, 'criar'], [AuthMiddleware::class]);
$router->get('/cliente/editar', [ClienteController::class, 'editar'], [AuthMiddleware::class]);
$router->post('/cliente/salvar', [ClienteController::class, 'salvar'], [AuthMiddleware::class]);
$router->post('/cliente/atualizar', [ClienteController::class, 'atualizar'], [AuthMiddleware::class]);
$router->post('/cliente/deletar', [ClienteController::class, 'deletar'], [AuthMiddleware::class]);
