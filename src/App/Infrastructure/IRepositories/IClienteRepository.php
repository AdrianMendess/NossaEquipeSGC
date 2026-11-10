<?php

declare(strict_types=1);

namespace Src\App\Infrastructure\IRepositories;

use Src\App\Models\Cliente;

// TODO: renomeie para o seu domínio (ex: IProdutoRepository, IClienteRepository)
interface IClienteRepository
{
    public function create(Cliente $cliente): Cliente;
    public function update(Cliente $cliente): Cliente;
    public function delete(string $id): bool;
    public function getAll(): array;
    public function getById(string $id): ?Cliente;
    public function count(): int;
}
