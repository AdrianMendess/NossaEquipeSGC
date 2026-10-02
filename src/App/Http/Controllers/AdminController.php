<?php

declare(strict_types=1);

namespace Src\App\Http\Controllers;

use Src\App\Dto\User\CriarUserDto;
use Src\App\Dto\User\EditUserDto;
use Src\App\Http\Exceptions\Usuario\UsuarioException;
use Src\App\Http\Requests\CriarUserFormRequest;
use Src\App\Http\Requests\EditUserFormRequest;
use Src\App\Services\IServices\IUserService;
use Src\App\Utils\Toast;
use Src\App\Utils\Url;
use Src\App\Utils\View;
use Src\Core\Logger;

class AdminController extends SharedController
{
    private IUserService $userService;

    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): string
    {
        $users = [];

        try {
            $users = $this->userService->getAllUsers();
        } catch (\Exception) {
            Url::redirect('/');
        }

        $toast = Toast::pull();

        $content = View::render(
            'Admin/index',
            [
                'users' => $users,
            ]
        );

        return parent::getPage(
            'PROJETO - ADMIN',
            $content,
            [
                'showSidebar' => true,
                'activePage' => 'admin',
            ],
            $toast
        );
    }

    public function editar(?string $id = null): string
    {
        $user = null;
        try {
            if ($id !== null) {
                $user = $this->userService->getUserById($id);
            }
        } catch (UsuarioException $exception) {
            Toast::error($exception->getMessage());
            Url::redirect('/admin');
        } catch (\Exception $e) {
            Logger::error('Erro ao buscar usuário: ' . $e->getMessage());
            Toast::error('Ocorreu um erro inesperado.');
            Url::redirect('/admin');
        }

        $toast = Toast::pull();

        $titulo = $user ? 'Editar Usuário' : 'Novo Usuário';
        $content = View::render('Admin/editar', ['user' => $user]);

        return self::getPage('PROJETO - ' . $titulo, $content, [
            'showSidebar' => true,
            'activePage' => 'admin',
        ], $toast);
    }

    public function criar(): void
    {
        $validated = (new CriarUserFormRequest($_POST))->redirectOnFail();

        try {
            $dto = CriarUserDto::fromArray($validated->validated());
            $this->userService->createUser($dto);
            Toast::success('Usuário criado com sucesso!');
            Url::redirect('/admin');
        } catch (UsuarioException $e) {
            Toast::error($e->getMessage());
            Url::redirect('/admin/novo');
        } catch (\Exception $e) {
            Logger::error('Erro ao criar usuário: ' . $e->getMessage());
            Toast::error('Ocorreu um erro inesperado.');
            Url::redirect('/admin');
        }
    }

    public function atualizar(string $id): void
    {
        $validated = (new EditUserFormRequest($_POST))->redirectOnFail();

        try {
            $usuarioLogadoId = (string) ($_SESSION['user_id'] ?? '');
            $dto = EditUserDto::fromArray($id, $validated->validated());
            $this->userService->updateUser($dto, $usuarioLogadoId);
            Toast::success('Usuário atualizado com sucesso!');
            Url::redirect('/admin');
        } catch (UsuarioException $e) {
            Toast::error($e->getMessage());
            Url::redirect($id ? "/admin/editar/{$id}" : '/admin/novo');
        } catch (\Exception $e) {
            Logger::error('Erro ao atualizar usuário: ' . $e->getMessage());
            Toast::error('Ocorreu um erro inesperado.');
            Url::redirect('/admin');
        }
    }

    public function deletar(): void
    {
        $userId = (string) ($_POST['user_id'] ?? '');

        try {
            $this->userService->deleteUser($userId);
            Toast::success('Usuário deletado com sucesso!');
        } catch (UsuarioException $e) {
            Toast::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error('Erro ao deletar usuário: ' . $e->getMessage());
            Toast::error('Ocorreu um erro inesperado.');
        }

        Url::redirect('/admin');
    }

    public function detalhes(string $id): string
    {
        $user = null;

        try {
            $user = $this->userService->getUserById($id);
        } catch (UsuarioException $e) {
            Toast::error($e->getMessage());
            Url::redirect('/admin');
        } catch (\Exception $e) {
            Logger::error('Erro ao buscar usuário: ' . $e->getMessage());
            Toast::error('Ocorreu um erro inesperado.');
            Url::redirect('/admin');
        }

        $toast = Toast::pull();

        $content = View::render('Admin/detalhes', ['user' => $user]);

        return parent::getPage('PROJETO - Detalhes do Usuário', $content, [
            'showSidebar' => true,
            'activePage' => 'admin',
        ], $toast);
    }
}
