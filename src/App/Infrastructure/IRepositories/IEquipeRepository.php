<?php

declare(strict_types=1);

namespace Src\App\Infrastructure\IRepositories;

use Src\App\Models\Equipe;

// TODO: renomeie para o seu domínio (ex: IProdutoRepository, IClienteRepository)
interface IEquipeRepository
{
    public function create(Equipe $equipe): Equipe;
    public function update(Equipe $equipe): Equipe;
    public function delete(string $id): bool;
    public function getAll(): array;
    public function getById(string $id): ?Equipe;
    public function count(): int;
}
