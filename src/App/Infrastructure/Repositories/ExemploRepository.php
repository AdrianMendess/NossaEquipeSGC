<?php

declare(strict_types=1);

namespace Src\App\Infrastructure\Repositories;

use PDO;
use Src\App\Http\Exceptions\Exemplo\ExemploException;
use Src\App\Infrastructure\IRepositories\IExemploRepository;
use Src\App\Models\Exemplo;
use Src\Core\Database;

// TODO: renomeie para o seu domínio e adapte os campos SQL
class ExemploRepository implements IExemploRepository
{
    private PDO $conn;

    public function __construct(Database $conn)
    {
        $this->conn = $conn->getConnection();
    }

    public function create(Exemplo $exemplo): Exemplo
    {
        $sql = 'INSERT INTO tb_exemplo (id, titulo, descricao, status, created_at, updated_at)
                VALUES (:id, :titulo, :descricao, :status, :created_at, :updated_at)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $exemplo->getId(), PDO::PARAM_STR);
        $stmt->bindValue(':titulo', $exemplo->getTitulo());
        $stmt->bindValue(':descricao', $exemplo->getDescricao());
        $stmt->bindValue(':status', $exemplo->getStatus(), PDO::PARAM_BOOL);
        $stmt->bindValue(':created_at', $exemplo->getCreatedAt());
        $stmt->bindValue(':updated_at', $exemplo->getUpdatedAt());
        $stmt->execute();

        return $exemplo;
    }

    public function update(Exemplo $exemplo): Exemplo
    {
        $sql = 'UPDATE tb_exemplo
                SET titulo = :titulo, descricao = :descricao,
                    status = :status, updated_at = :updated_at, version = version + 1
                WHERE id = :id AND version = :expectedVersion';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':titulo', $exemplo->getTitulo());
        $stmt->bindValue(':descricao', $exemplo->getDescricao());
        $stmt->bindValue(':status', $exemplo->getStatus(), PDO::PARAM_BOOL);
        $stmt->bindValue(':updated_at', $exemplo->getUpdatedAt());
        $stmt->bindValue(':id', $exemplo->getId(), PDO::PARAM_STR);
        $stmt->bindValue(':expectedVersion', $exemplo->getVersion(), PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw ExemploException::conflitoDeEdicao();
        }

        return $exemplo;
    }

    public function getAll(): array
    {
        $sql = 'SELECT id, titulo, descricao, status, version FROM tb_exemplo ORDER BY id DESC';
        $stmt = $this->conn->query($sql);
        $itens = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $itens[] = new Exemplo(
                titulo: $row['titulo'],
                descricao: $row['descricao'],
                status: (bool) $row['status'],
                created_at: null,
                updated_at: null,
                id: $row['id'],
                version: (int) $row['version'],
            );
        }

        return $itens;
    }

    public function getById(string $id): ?Exemplo
    {
        $sql = 'SELECT * FROM tb_exemplo WHERE id = :id';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Exemplo(
                titulo: $row['titulo'],
                descricao: $row['descricao'],
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
        $sql = 'DELETE FROM tb_exemplo WHERE id = :id';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function count(): int
    {
        $stmt = $this->conn->query('SELECT COUNT(*) FROM tb_exemplo');
        return (int) $stmt->fetchColumn();
    }
}
