<?php

declare(strict_types=1);

namespace Src\App\Services;

use Src\App\Enums\Status;
use Src\App\Http\Exceptions\Usuario\UsuarioException;
use Src\App\Infrastructure\IRepositories\IUserRepository;
use Src\App\Models\User;
use Src\App\Services\IServices\IUserService;
use Src\App\ValueObjects\Senha;
use Src\Core\Transaction;

class UserService implements IUserService
{
    private IUserRepository $repository;

    public function __construct(IUserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createUser(array $data): User
    {
        if ($this->repository->getUserByEmail($data['email']) !== null) {
            throw UsuarioException::emailInvalido();
        }

        $user = User::fromArray([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'senha' => $data['senha'],
            'perfil' => $data['perfil'],
            'status' => Status::ATIVO->value,
        ]);

        return Transaction::run(fn () => $this->repository->createUser($user));
    }

    public function deleteUser(string $id): void
    {
        if ($this->repository->getUserById($id) === null) {
            throw UsuarioException::naoEncontrado();
        }

        Transaction::run(fn () => $this->repository->deleteUser($id), 'Erro ao deletar o usuário.');
    }

    public function updateUser(string $id, array $data, ?string $usuarioLogadoId = null): User
    {
        $userExist = $this->repository->getUserByEmail($data['email']);
        if ($userExist !== null && $userExist->getId() !== $id) {
            throw UsuarioException::emailInvalido('O e-mail informado não está disponível.');
        }

        $novoStatus = Status::validar((int) $data['status']);

        if ($usuarioLogadoId !== null && $usuarioLogadoId === $id && $novoStatus === Status::INATIVO) {
            throw UsuarioException::naoPodeDesativarProprioUsuario();
        }

        $existente = $this->getUserById($id);

        $user = User::fromArray([
            'id' => $id,
            'nome' => $data['nome'],
            'email' => $data['email'],
            'senha' => empty($data['senha']) ? $existente->getSenha() : $data['senha'],
            'perfil' => $data['perfil'],
            'status' => $novoStatus->value,
            'created_at' => $existente->getCreatedAt(),
            'version' => (int) $data['version'],
        ]);

        return Transaction::run(fn () => $this->repository->updateUser($user));
    }

    public function getUserById(string $id): User
    {
        $user = $this->repository->getUserById($id);

        if ($user === null) {
            throw UsuarioException::naoEncontrado();
        }

        return $user;
    }

    public function getUserByEmail(string $email): User
    {
        $user = $this->repository->getUserByEmail($email);

        if ($user === null) {
            throw UsuarioException::naoEncontrado('Email não cadastrado.');
        }

        return $user;
    }

    public function getAllUsers(): array
    {
        return $this->repository->getAllUser();
    }

    public function atualizarSenha(string $id, string $novaSenha): User
    {
        $user = $this->getUserById($id);
        $user->atualizaSenha(Senha::senha($novaSenha));

        return Transaction::run(fn () => $this->repository->updateUser($user));
    }

    public function redefinirSenha(string $email, string $novaSenha): void
    {
        $user = $this->getUserByEmail($email);
        $this->atualizarSenha($user->getId(), $novaSenha);
    }

    public function verificarSenhaAtual(string $id, string $senhaAtual): bool
    {
        return $this->getUserById($id)->verificaSenha($senhaAtual);
    }

    public function paginacao(): array
    {
        $totalUsuarios = $this->repository->countUsers();
        $limit = 10;
        $totalPaginas = ceil($totalUsuarios / $limit);
        $paginaAtual = isset($_GET['pg']) ? (int) $_GET['pg'] : 1;
        $paginaAtual = max(1, min($paginaAtual, $totalPaginas));
        $start = ($paginaAtual - 1) * $limit;

        $usuarios = $this->repository->getAllUsersPaginado($limit, $start);

        return [
            'usuarios' => $usuarios,
            'total_paginas' => $totalPaginas,
            'pagina_atual' => $paginaAtual,
        ];
    }
}
