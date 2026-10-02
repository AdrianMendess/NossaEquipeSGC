<?php

declare(strict_types=1);

namespace Src\App\Models;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Src\App\Enums\Perfil;
use Src\App\Enums\Status;
use Src\App\Http\Exceptions\Usuario\UsuarioException;
use Src\App\ValueObjects\Senha;

class User
{
    private string $id;
    private string $nome;
    private string $email;
    private Senha $senha;

    private Perfil $perfil = Perfil::USER;

    private Status $status = Status::ATIVO;

    private ?DateTimeImmutable $createdAt = null;
    private ?DateTimeImmutable $updatedAt = null;
    private int $version = 1;

    public function __construct(
        string $nome,
        string $email,
        Senha $senha,
        ?Perfil $perfil,
        ?Status $status,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
        ?string $id = null,
        int $version = 1
    ) {
        $this->setId($id);
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->perfil = $perfil ?? Perfil::USER;
        $this->status = $status ?? Status::ATIVO;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
        $this->version = $version;
    }

    public static function fromArray(array $data): self
    {
        $senha = $data['senha'] instanceof Senha
            ? $data['senha']
            : Senha::senha((string) $data['senha']);

        return new self(
            nome: $data['nome'],
            email: $data['email'],
            senha: $senha,
            perfil: isset($data['perfil']) ? Perfil::validar((int) $data['perfil']) : null,
            status: isset($data['status']) ? Status::validar((int) $data['status']) : null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            id: $data['id'] ?? null,
            version: isset($data['version']) ? (int) $data['version'] : 1,
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSenha(): Senha
    {
        return $this->senha;
    }

    public function getPerfil(): Perfil
    {
        return $this->perfil;
    }

    public function isAdmin(): bool
    {
        return $this->perfil === Perfil::ADMIN;
    }

    public function isAtivo(): bool
    {
        return $this->status === Status::ATIVO;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setId(?string $id): void
    {
        $this->id = $id ?? Uuid::uuid7()->toString();
    }

    public function setNome(string $nome): void
    {
        $nome = preg_replace("/[\'\"<>]/", '', $nome);
        $this->nome = $nome;
    }

    public function setEmail(string $email): void
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw UsuarioException::emailInvalido('Formato de e-mail inválido.');
        }
        $this->email = $email;
    }

    public function setPerfil(Perfil $perfil): void
    {
        $this->perfil = $perfil;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    public function atualizaSenha(Senha $novaSenha): void
    {
        $this->senha = $novaSenha;
    }

    public function verificaSenha(string $senha): bool
    {
        return $this->senha->confere($senha);
    }
}
