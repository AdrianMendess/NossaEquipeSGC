<?php

declare(strict_types=1);

namespace Src\App\Services;

use Src\App\Dto\Equipe\CriarEquipeDto;
use Src\App\Dto\Equipe\EditEquipeDto;
use Src\App\Http\Exceptions\Equipe\EquipeException;
use Src\App\Infrastructure\IRepositories\IEquipeRepository;
use Src\App\Models\Equipe;
use Src\App\Services\IServices\IEquipeService;
use Src\Core\Storage;
use Src\Core\Transaction;

class EquipeService implements IEquipeService
{
    public function __construct(
        private IEquipeRepository $repository
    ) {
    }

    public function create(CriarEquipeDto $dto): Equipe
    {
        $agora = date('Y-m-d H:i:s');

        $novo = Equipe::fromArray([
            'foto'       => $dto->foto,
            'nome'       => $dto->nome,
            'cargo'      => $dto->cargo,
            'status'     => true,
            'created_at' => $agora,
            'updated_at' => $agora,
        ]);

        return Transaction::run(fn () => $this->repository->create($novo));
    }

    public function update(EditEquipeDto $dto): Equipe
    {
        $existente = $this->repository->getById($dto->id);

        if ($existente === null) {
            throw EquipeException::naoEncontrado();
        }

        $foto = $dto->foto ?? $existente->getFoto();

        if ($dto->foto !== null && $existente->getFoto() && $dto->foto !== $existente->getFoto()) {
            (new Storage())->delete($existente->getFoto());
        }

        $atualizado = Equipe::fromArray([
            'id'         => $dto->id,
            'foto'       => $foto,
            'nome'       => $dto->nome,
            'cargo'      => $dto->cargo,
            'status'     => $existente->getStatus(),
            'created_at' => $existente->getCreatedAt(),
            'updated_at' => date('Y-m-d H:i:s'),
            'version'    => $dto->version ?? $existente->getVersion(),
        ]);

        return Transaction::run(fn () => $this->repository->update($atualizado));
    }

    public function delete(string $id): bool
    {
        $existente = $this->repository->getById($id);

        if ($existente === null) {
            throw EquipeException::naoEncontrado();
        }

        if ($existente->getFoto()) {
            (new Storage())->delete($existente->getFoto());
        }

        return Transaction::run(fn () => $this->repository->delete($id));
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function getById(string $id): Equipe
    {
        $equipe = $this->repository->getById($id);

        if ($equipe === null) {
            throw EquipeException::naoEncontrado();
        }

        return $equipe;
    }

    public function count(): int
    {
        return $this->repository->count();
    }
}
