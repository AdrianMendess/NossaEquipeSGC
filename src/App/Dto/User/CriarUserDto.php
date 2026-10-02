<?php

declare(strict_types=1);

namespace Src\App\Dto\User;

final class CriarUserDto
{
    public function __construct(
        public readonly string $nome,
        public readonly string $email,
        public readonly string $senha,
        public readonly int $perfil,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            nome: $data['nome'],
            email: $data['email'],
            senha: $data['senha'],
            perfil: (int) $data['perfil'],
        );
    }
}
