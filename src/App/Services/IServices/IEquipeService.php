<?php

declare(strict_types=1);

namespace Src\App\Services\IServices;

use Src\App\Dto\Equipe\CriarEquipeDto;
use Src\App\Dto\Equipe\EditEquipeDto;
use Src\App\Models\Equipe;

interface IEquipeService
{
    public function create(CriarEquipeDto $dto): Equipe;
    public function update(EditEquipeDto $dto): Equipe;
    public function delete(string $id): bool;
    public function getAll(): array;
    public function getById(string $id): Equipe;
    public function count(): int;
}
