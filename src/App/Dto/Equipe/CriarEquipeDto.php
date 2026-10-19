<?php

declare(strict_types=1);

namespace Src\App\Dto\Equipe;

final class CriarEquipeDto
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
