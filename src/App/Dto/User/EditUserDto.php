<?php

declare(strict_types=1);

namespace Src\App\Dto\User;

final class EditUserDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $nome,
        public readonly string $email,
        public readonly ?string $senha,
        public readonly int $perfil,
        public readonly int $status,
        public readonly int $version,
    ) {
    }

    public static function fromArray(string $id, array $data): self
    {
        return new self(
            id: $id,
            nome: $data['nome'],
            email: $data['email'],
            senha: empty($data['senha']) ? null : $data['senha'],
            perfil: (int) $data['perfil'],
            status: (int) $data['status'],
            version: (int) $data['version'],
        );
    }
}
