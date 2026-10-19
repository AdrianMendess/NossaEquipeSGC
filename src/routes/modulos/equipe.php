<?php

declare(strict_types=1);

use Src\App\Http\Controllers\EquipeController;
use Src\App\Http\Middleware\AuthMiddleware;

// TODO: renomeie as rotas para o seu domínio (ex: /produtos, /clientes)
$router->get('/equipe', [EquipeController::class, 'index'], [AuthMiddleware::class]);
$router->get('/equipe/criar', [EquipeController::class, 'criar'], [AuthMiddleware::class]);
$router->get('/equipe/editar', [EquipeController::class, 'editar'], [AuthMiddleware::class]);
$router->post('/equipe/salvar', [EquipeController::class, 'salvar'], [AuthMiddleware::class]);
$router->post('/equipe/atualizar', [EquipeController::class, 'atualizar'], [AuthMiddleware::class]);
$router->post('/equipe/deletar', [EquipeController::class, 'deletar'], [AuthMiddleware::class]);
