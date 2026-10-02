<?php

declare(strict_types=1);

namespace Src\App\Dto\Exemplo;

final class CriarExemploDto
{
    public function __construct(
        public readonly string $titulo,
        public readonly ?string $descricao,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            titulo: $data['titulo'],
            descricao: $data['descricao'] ?? null,
        );
    }
}
