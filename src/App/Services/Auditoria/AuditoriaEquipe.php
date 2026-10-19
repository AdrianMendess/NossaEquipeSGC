<?php

declare(strict_types=1);

namespace Src\App\Services\Auditoria;

use Src\App\Infrastructure\IRepositories\IEquipeRepository;
use Src\App\Models\Equipe;
use Src\App\Services\IServices\IAuditoriaService;

// TODO: renomeie para o seu domínio (ex: AuditoriaProduto, AuditoriaCliente)
class AuditoriaEquipe implements IEquipeRepository
{
    public function __construct(
        private IEquipeRepository $repository,
        private IAuditoriaService $auditoriaService
    ) {
    }

    public function create(Equipe $equipe): Equipe
    {
        $criado = $this->repository->create($equipe);
        $this->auditoriaService->registrar(
            $_SESSION['user_id'] ?? null,
            'Equipe',
            'create',
            $criado->getId(),
            ['titulo' => $criado->getTitulo(), 'descricao' => $criado->getDescricao()]
        );
        return $criado;
    }

    public function update(Equipe $equipe): Equipe
    {
        $antes = $this->repository->getById($equipe->getId());
        $atualizado = $this->repository->update($equipe);
        $this->auditoriaService->registrar(
            $_SESSION['user_id'] ?? null,
            'Equipe',
            'update',
            $atualizado->getId(),
            [
                'antes'  => ['titulo' => $antes?->getTitulo(), 'descricao' => $antes?->getDescricao()],
                'depois' => ['titulo' => $atualizado->getTitulo(), 'descricao' => $atualizado->getDescricao()],
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
                'Equipe',
                'delete',
                $id,
                ['id' => 'Equipe com id ' . $id . ' deletado']
            );
        }
        return $deleted;
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function getById(string $id): ?Equipe
    {
        return $this->repository->getById($id);
    }

    public function count(): int
    {
        return $this->repository->count();
    }
}