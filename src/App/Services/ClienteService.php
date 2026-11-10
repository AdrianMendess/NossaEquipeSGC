<?php

declare(strict_types=1);

namespace Src\App\Services;

use Src\App\Dto\Cliente\CriarClienteDto;
use Src\App\Dto\Cliente\EditClienteDto;
use Src\App\Http\Exceptions\Cliente\ClienteException;
use Src\App\Infrastructure\IRepositories\IClienteRepository;
use Src\App\Models\Cliente;
use Src\App\Services\IServices\IClienteService;
use Src\Core\Transaction;

class ClienteService implements IClienteService
{
    public function __construct(
        private IClienteRepository $repository
    ) {
    }

    public function create(CriarClienteDto $dto): Cliente
    {
        $agora = date('Y-m-d H:i:s');

        $novo = Cliente::fromArray([
            'nome' => $dto->nome,
            'tipo' => $dto->tipo,
            'status' => true,
            'created_at' => $agora,
            'updated_at' => $agora,
        ]);

        return Transaction::run(fn () => $this->repository->create($novo));
    }

    public function update(EditClienteDto $dto): Cliente
    {
        $existente = $this->repository->getById($dto->id);

        if ($existente === null) {
            throw ClienteException::naoEncontrado();
        }

        $atualizado = Cliente::fromArray([
            'id' => $dto->id,
            'nome' => $dto->nome,
            'tipo' => $dto->tipo,
            'status' => $existente->getStatus(),
            'created_at' => $existente->getCreatedAt(),
            'updated_at' => date('Y-m-d H:i:s'),
            'version' => $dto->version ?? $existente->getVersion(),
        ]);

        return Transaction::run(fn () => $this->repository->update($atualizado));
    }

    public function delete(string $id): bool
    {
        $existente = $this->repository->getById($id);

        if ($existente === null) {
            throw ClienteException::naoEncontrado();
        }

        return Transaction::run(fn () => $this->repository->delete($id));
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function getById(string $id): Cliente
    {
        $cliente = $this->repository->getById($id);

        if ($cliente === null) {
            throw ClienteException::naoEncontrado();
        }

        return $cliente;
    }

    public function count(): int
    {
        return $this->repository->count();
    }
}
