<?php

declare(strict_types=1);

namespace Src\App\Dto\Equipe;

final class EditEquipeDto
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $foto,
        public readonly string $nome,
        public readonly ?string $cargo,
        public readonly ?int $version,
    ) {
    }

    public static function fromArray(string $id, array $data): self
    {
        return new self(
            id: $id,
            foto: is_string($data['foto'] ?? null) ? $data['foto'] : null,
            nome: $data['nome'],
            cargo: $data['cargo'] ?? null,
            version: isset($data['version']) ? (int) $data['version'] : null,
        );
    }
}
