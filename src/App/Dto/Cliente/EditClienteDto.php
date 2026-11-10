<?php

declare(strict_types=1);

namespace Src\App\Dto\Cliente;

final class EditClienteDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $nome,
        public readonly ?string $tipo,
        public readonly ?int $version,
        public readonly bool $status,
    ) {
    }

    public static function fromArray(string $id, array $data): self
    {
        return new self(
            id: $id,
            nome: $data['nome'],
            tipo: $data['tipo'] ?? null,
            version: isset($data['version']) ? (int) $data['version'] : null,
            status: isset($data['status']) ? (bool) (int) $data['status'] : false      
        );
    }
}
