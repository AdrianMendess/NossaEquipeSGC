<?php

declare(strict_types=1);

namespace Src\App\Dto\Equipe;

final class CriarEquipeDto
{
    public function __construct(
        public readonly string $nome,
        public readonly ?string $cargo,
        public readonly ?string $foto,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            nome: $data['nome'],
            cargo: $data['cargo'] ?? null,
            foto: is_string($data['foto'] ?? null) ? $data['foto'] : null,
        );
    }
}
