<?php

declare(strict_types=1);

namespace Src\App\Services;

use Src\App\Dto\User\CriarUserDto;
use Src\App\Dto\User\EditUserDto;
use Src\App\Enums\Status;
use Src\App\Http\Exceptions\Usuario\UsuarioException;
use Src\App\Infrastructure\IRepositories\IUserRepository;
use Src\App\Models\User;
use Src\App\Services\IServices\IUserService;
use Src\App\ValueObjects\Senha;
use Src\Core\Transaction;

class UserService implements IUserService
{
    public function __construct(
        private IUserRepository $repository
    ) {
    }

    public function createUser(CriarUserDto $dto): User
    {
        if ($this->repository->getUserByEmail($dto->email) !== null) {
            throw UsuarioException::emailInvalido();
        }

        $user = User::fromArray([
            'nome' => $dto->nome,
            'email' => $dto->email,
            'senha' => $dto->senha,
            'perfil' => $dto->perfil,
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

    public function updateUser(EditUserDto $dto, ?string $usuarioLogadoId = null): User
    {
        $userExist = $this->repository->getUserByEmail($dto->email);
        if ($userExist !== null && $userExist->getId() !== $dto->id) {
            throw UsuarioException::emailInvalido('O e-mail informado não está disponível.');
        }

        $novoStatus = Status::validar($dto->status);

        if ($usuarioLogadoId !== null && $usuarioLogadoId === $dto->id && $novoStatus === Status::INATIVO) {
            throw UsuarioException::naoPodeDesativarProprioUsuario();
        }

        $existente = $this->getUserById($dto->id);

        $user = User::fromArray([
            'id' => $dto->id,
            'nome' => $dto->nome,
            'email' => $dto->email,
            'senha' => $dto->senha === null ? $existente->getSenha() : $dto->senha,
            'perfil' => $dto->perfil,
            'status' => $novoStatus->value,
            'created_at' => $existente->getCreatedAt(),
            'version' => $dto->version,
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

    public function trocarSenha(string $id, string $senhaAtual, string $novaSenha): User
    {
        if (! $this->verificarSenhaAtual($id, $senhaAtual)) {
            throw UsuarioException::senhaAtualIncorreta();
        }

        return $this->atualizarSenha($id, $novaSenha);
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
