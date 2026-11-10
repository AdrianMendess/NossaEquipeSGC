<?php

declare(strict_types=1);

namespace Src\App\Infrastructure\Repositories;

use PDO;
use Src\App\Http\Exceptions\Cliente\ClienteException;
use Src\App\Infrastructure\IRepositories\IClienteRepository;
use Src\App\Models\Cliente;
use Src\Core\Database;

// TODO: renomeie para o seu domínio e adapte os campos SQL
class ClienteRepository implements IClienteRepository
{
    private PDO $conn;

    public function __construct(Database $conn)
    {
        $this->conn = $conn->getConnection();
    }

    public function create(Cliente $cliente): Cliente
    {
        $sql = 'INSERT INTO tb_cliente (id, nome, tipo, status, created_at, updated_at)
                VALUES (:id, :nome, :tipo, :status, :created_at, :updated_at)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $cliente->getId(), PDO::PARAM_STR);
        $stmt->bindValue(':nome', $cliente->getNome());
        $stmt->bindValue(':tipo', $cliente->getTipo());
        $stmt->bindValue(':status', $cliente->getStatus(), PDO::PARAM_BOOL);
        $stmt->bindValue(':created_at', $cliente->getCreatedAt());
        $stmt->bindValue(':updated_at', $cliente->getUpdatedAt());
        $stmt->execute();

        return $cliente;
    }

    public function update(Cliente $cliente): Cliente
    {
        $sql = 'UPDATE tb_cliente
                SET nome = :nome, tipo = :tipo,
                    status = :status, updated_at = :updated_at, version = version + 1
                WHERE id = :id AND version = :expectedVersion';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':nome', $cliente->getNome());
        $stmt->bindValue(':tipo', $cliente->getTipo());
        $stmt->bindValue(':status', $cliente->getStatus(), PDO::PARAM_BOOL);
        $stmt->bindValue(':updated_at', $cliente->getUpdatedAt());
        $stmt->bindValue(':id', $cliente->getId(), PDO::PARAM_STR);
        $stmt->bindValue(':expectedVersion', $cliente->getVersion(), PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw ClienteException::conflitoDeEdicao();
        }

        return $cliente;
    }

    public function getAll(): array
    {
        $sql = 'SELECT id, nome, tipo, status, version FROM tb_cliente ORDER BY id DESC';
        $stmt = $this->conn->query($sql);
        $itens = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $itens[] = new Cliente(
                nome: $row['nome'],
                tipo: $row['tipo'],
                status: (bool) $row['status'],
                created_at: null,
                updated_at: null,
                id: $row['id'],
                version: (int) $row['version'],
            );
        }

        return $itens;
    }

    public function getById(string $id): ?Cliente
    {
        $sql = 'SELECT * FROM tb_cliente WHERE id = :id';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Cliente(
                nome: $row['nome'],
                tipo: $row['tipo'],
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
        $sql = 'DELETE FROM tb_cliente WHERE id = :id';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function count(): int
    {
        $stmt = $this->conn->query('SELECT COUNT(*) FROM tb_cliente');
        return (int) $stmt->fetchColumn();
    }
}
