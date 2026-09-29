<?php

declare(strict_types=1);

namespace Src\App\Services;

use Src\App\Http\Exceptions\Exemplo\ExemploException;
use Src\App\Infrastructure\IRepositories\IExemploRepository;
use Src\App\Models\Exemplo;
use Src\App\Services\IServices\IExemploService;
use Src\Core\Transaction;

class ExemploService implements IExemploService
{
    public function __construct(
        private IExemploRepository $repository
    ) {
    }

    public function create(array $data): Exemplo
    {
        $agora = date('Y-m-d H:i:s');

        $novo = Exemplo::fromArray([
            'titulo' => $data['titulo'],
            'descricao' => $data['descricao'] ?? null,
            'status' => true,
            'created_at' => $agora,
            'updated_at' => $agora,
        ]);

        return Transaction::run(fn () => $this->repository->create($novo));
    }

    public function update(string $id, array $data): Exemplo
    {
        $existente = $this->repository->getById($id);

        if ($existente === null) {
            throw ExemploException::naoEncontrado();
        }

        $atualizado = Exemplo::fromArray([
            'id' => $id,
            'titulo' => $data['titulo'],
            'descricao' => $data['descricao'] ?? null,
            'status' => $existente->getStatus(),
            'created_at' => $existente->getCreatedAt(),
            'updated_at' => date('Y-m-d H:i:s'),
            'version' => isset($data['version']) ? (int) $data['version'] : $existente->getVersion(),
        ]);

        return Transaction::run(fn () => $this->repository->update($atualizado));
    }

    public function delete(string $id): bool
    {
        $existente = $this->repository->getById($id);

        if ($existente === null) {
            throw ExemploException::naoEncontrado();
        }

        return Transaction::run(fn () => $this->repository->delete($id));
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function getById(string $id): ?Exemplo
    {
        return $this->repository->getById($id);
    }

    public function count(): int
    {
        return $this->repository->count();
    }
}
