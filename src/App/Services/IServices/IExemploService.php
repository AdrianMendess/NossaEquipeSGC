<?php

declare(strict_types=1);

namespace Src\App\Services\IServices;

use Src\App\Dto\Exemplo\CriarExemploDto;
use Src\App\Dto\Exemplo\EditExemploDto;
use Src\App\Models\Exemplo;

interface IExemploService
{
    public function create(CriarExemploDto $dto): Exemplo;
    public function update(EditExemploDto $dto): Exemplo;
    public function delete(string $id): bool;
    public function getAll(): array;
    public function getById(string $id): Exemplo;
    public function count(): int;
}
