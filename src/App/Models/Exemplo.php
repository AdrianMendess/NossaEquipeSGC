<?php

declare(strict_types=1);

namespace Src\App\Models;

use Ramsey\Uuid\Uuid;

// TODO: renomeie esta classe e seus campos para o seu domínio
class Exemplo
{
    private string $id;
    private string $titulo;
    private ?string $descricao;
    private ?bool $status;
    private ?string $created_at;
    private ?string $updated_at;
    private int $version = 1;

    public function __construct(
        string $titulo,
        ?string $descricao = null,
        ?bool $status = null,
        ?string $created_at = null,
        ?string $updated_at = null,
        ?string $id = null,
        int $version = 1
    ) {
        $this->setId($id);
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->status = $status;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->version = $version;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            titulo: $data['titulo'],
            descricao: $data['descricao'] ?? null,
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

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): void
    {
        $this->titulo = $titulo;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
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
