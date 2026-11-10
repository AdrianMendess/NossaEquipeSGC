<?php

declare(strict_types=1);

namespace Src\App\Dto\Cliente;

final class CriarClienteDto
{
    public function __construct(
        public readonly string $nome,
        public readonly ?string $tipo,
        public readonly bool $status = true,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            nome: $data['nome'],
            tipo: $data['tipo'] ?? null,
            status: filter_var($data['status'] ?? true, FILTER_VALIDATE_BOOLEAN)
        );
    }
}
