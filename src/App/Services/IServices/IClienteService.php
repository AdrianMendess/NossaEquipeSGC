<?php

declare(strict_types=1);

namespace Src\App\Services\IServices;

use Src\App\Dto\Cliente\CriarClienteDto;
use Src\App\Dto\Cliente\EditClienteDto;
use Src\App\Models\Cliente;

interface IClienteService
{
    public function create(CriarClienteDto $dto): Cliente;
    public function update(EditClienteDto $dto): Cliente;
    public function delete(string $id): bool;
    public function getAll(): array;
    public function getById(string $id): Cliente;
    public function count(): int;
}
