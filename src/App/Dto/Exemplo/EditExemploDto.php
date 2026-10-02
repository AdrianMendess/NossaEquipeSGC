<?php

declare(strict_types=1);

namespace Src\App\Dto\Exemplo;

final class EditExemploDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $titulo,
        public readonly ?string $descricao,
        public readonly ?int $version,
    ) {
    }

    public static function fromArray(string $id, array $data): self
    {
        return new self(
            id: $id,
            titulo: $data['titulo'],
            descricao: $data['descricao'] ?? null,
            version: isset($data['version']) ? (int) $data['version'] : null,
        );
    }
}
