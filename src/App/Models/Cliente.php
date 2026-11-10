<?php

declare(strict_types=1);

namespace Src\App\Models;

use Ramsey\Uuid\Uuid;

// TODO: renomeie esta classe e seus campos para o seu domínio
class Cliente
{
    private string $id;
    private string $nome;
    private ?string $tipo;
    private ?bool $status;
    private ?string $created_at;
    private ?string $updated_at;
    private int $version = 1;

    public function __construct(
        string $nome,
        ?string $tipo = null,
        ?bool $status = null,
        ?string $created_at = null,
        ?string $updated_at = null,
        ?string $id = null,
        int $version = 1
    ) {
        $this->setId($id);
        $this->nome = $nome;
        $this->tipo = $tipo;
        $this->status = $status;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->version = $version;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            nome: $data['nome'],
            tipo: $data['tipo'] ?? null,
            status: $data['status'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            id: $data['id'] ?? null,
            version: isset($data['version']) ? (int) $data['version'] : 1,
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id ?? Uuid::uuid7()->toString();
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): void
    {
        $this->tipo = $tipo;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): void
    {
        $this->status = $status;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(?string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function getVersion(): int
    {
        return $this->version;
    }
}
