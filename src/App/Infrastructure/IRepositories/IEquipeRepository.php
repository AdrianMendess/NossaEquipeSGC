<?php

declare(strict_types=1);

namespace Src\App\Infrastructure\IRepositories;

use Src\App\Models\Equipe;

interface IEquipeRepository
{
    public function create(Equipe $equipe): Equipe;
    public function update(Equipe $equipe): Equipe;
    public function delete(string $id): bool;
    public function getAll(?string $busca = null, string $ordem = 'ASC'): array;
    public function getById(string $id): ?Equipe;
    public function count(): int;
}
