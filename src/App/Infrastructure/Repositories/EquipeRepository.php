<?php

declare(strict_types=1);

namespace Src\App\Infrastructure\Repositories;

use PDO;
use Src\App\Http\Exceptions\Equipe\EquipeException;
use Src\App\Infrastructure\IRepositories\IEquipeRepository;
use Src\App\Models\Equipe;
use Src\Core\Database;

class EquipeRepository implements IEquipeRepository
{
    private PDO $conn;

    public function __construct(Database $conn)
    {
        $this->conn = $conn->getConnection();
    }

    public function create(Equipe $equipe): Equipe
    { 
        $sql = 'INSERT INTO tb_equipe (id, foto, nome, cargo, status, created_at, updated_at)
                VALUES (:id, :foto, :nome, :cargo, :status, :created_at, :updated_at)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $equipe->getId(), PDO::PARAM_STR);
        $stmt->bindValue(':foto', $equipe->getFoto());
        $stmt->bindValue(':nome', $equipe->getNome());
        $stmt->bindValue(':cargo', $equipe->getCargo());
        $stmt->bindValue(':status', $equipe->getStatus(), PDO::PARAM_BOOL);
        $stmt->bindValue(':created_at', $equipe->getCreatedAt());
        $stmt->bindValue(':updated_at', $equipe->getUpdatedAt());
        $stmt->execute();

        return $equipe;
    }

    public function update(Equipe $equipe): Equipe
    {
        $sql = 'UPDATE tb_equipe
                SET foto = :foto, nome = :nome, cargo = :cargo,
                    status = :status, updated_at = :updated_at, version = version + 1
                WHERE id = :id AND version = :expectedVersion';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':foto', $equipe->getFoto());
        $stmt->bindValue(':nome', $equipe->getNome());
        $stmt->bindValue(':cargo', $equipe->getCargo());
        $stmt->bindValue(':status', $equipe->getStatus(), PDO::PARAM_BOOL);
        $stmt->bindValue(':updated_at', $equipe->getUpdatedAt());
        $stmt->bindValue(':id', $equipe->getId(), PDO::PARAM_STR);
        $stmt->bindValue(':expectedVersion', $equipe->getVersion(), PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw EquipeException::conflitoDeEdicao();
        }

        return $equipe;
    }

    public function getAll(): array
    {
        $sql = 'SELECT id, foto, nome, cargo, status, version FROM tb_equipe ORDER BY id DESC';
        $stmt = $this->conn->query($sql);
        $itens = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $itens[] = new Equipe(
                foto: $row['foto'],
                nome: $row['nome'],
                cargo: $row['cargo'],
                status: (bool) $row['status'],
                created_at: null,
                updated_at: null,
                id: $row['id'],
                version: (int) $row['version'],
            );
        }

        return $itens;
    }

    public function getById(string $id): ?Equipe
    {
        $sql = 'SELECT * FROM tb_equipe WHERE id = :id';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Equipe(
                foto: $row['foto'],
                nome: $row['nome'],
                cargo: $row['cargo'],
                status: (bool) $row['status'],
                created_at: $row['created_at'],
                updated_at: $row['updated_at'],
                id: $row['id'],
                version: (int) $row['version'],
            );
        }

        return null;
    }

    public function delete(string $id): bool
    {
        $sql = 'DELETE FROM tb_equipe WHERE id = :id';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function count(): int
    {
        $stmt = $this->conn->query('SELECT COUNT(*) FROM tb_equipe');
        return (int) $stmt->fetchColumn();
    }
}
