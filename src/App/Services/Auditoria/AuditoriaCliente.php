<?php

declare(strict_types=1);

namespace Src\App\Services\Auditoria;

use Src\App\Infrastructure\IRepositories\IClienteRepository;
use Src\App\Models\Cliente;
use Src\App\Services\IServices\IAuditoriaService;

// TODO: renomeie para o seu domínio (ex: AuditoriaProduto, AuditoriaCliente)
class AuditoriaCliente implements IClienteRepository
{
    public function __construct(
        private IClienteRepository $repository,
        private IAuditoriaService $auditoriaService
    ) {
    }

    public function create(Cliente $cliente): Cliente
    {
        $criado = $this->repository->create($cliente);
        $this->auditoriaService->registrar(
            $_SESSION['user_id'] ?? null,
            'Cliente',
            'create',
            $criado->getId(),
            ['nome' => $criado->getNome(), 'tipo' => $criado->getTipo()]
        );
        return $criado;
    }

    public function update(Cliente $cliente): Cliente
    {
        $antes = $this->repository->getById($cliente->getId());
        $atualizado = $this->repository->update($cliente);
        $this->auditoriaService->registrar(
            $_SESSION['user_id'] ?? null,
            'Cliente',
            'update',
            $atualizado->getId(),
            [
                'antes'  => ['nome' => $antes?->getNome(), 'tipo' => $antes?->getTipo()],
                'depois' => ['nome' => $atualizado->getNome(), 'tipo' => $atualizado->getTipo()],
            ]
        );
        return $atualizado;
    }

    public function delete(string $id): bool
    {
        $deleted = $this->repository->delete($id);
        if ($deleted) {
            $this->auditoriaService->registrar(
                $_SESSION['user_id'] ?? null,
                'Cliente',
                'delete',
                $id,
                ['id' => 'Cliente com id ' . $id . ' deletado']
            );
        }
        return $deleted;
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function getById(string $id): ?Cliente
    {
        return $this->repository->getById($id);
    }

    public function count(): int
    {
        return $this->repository->count();
    }
}