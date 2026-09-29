<?php

declare(strict_types=1);

namespace Src\App\Infrastructure\Repositories;

use PDO;
use Src\App\Enums\Perfil;
use Src\App\Enums\Status;
use Src\App\Http\Exceptions\Usuario\UsuarioException;
use Src\App\Infrastructure\IRepositories\IUserRepository;
use Src\App\Models\User;
use Src\App\ValueObjects\Senha;
use Src\Core\Database;

class UserRepository implements IUserRepository
{
    private PDO $conn;

    public function __construct(Database $conn)
    {
        $this->conn = $conn->getConnection();
    }

    public function createUser(User $user): User
    {
        $sql = 'INSERT INTO tb_usuarios (id, nome, email, senha, perfil_id, status, created_at)
                VALUES (:id, :name, :email, :password, :perfil_id, :status, :created_at)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $user->getId(), PDO::PARAM_STR);
        $stmt->bindValue(':name', $user->getNome());
        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':password', $user->getSenha()->hash());
        $stmt->bindValue(':perfil_id', $user->getPerfil()->value);
        $stmt->bindValue(':status', $user->getStatus()->value);
        $stmt->bindValue(':created_at', (new \DateTimeImmutable())->format('Y-m-d H:i:s'));
        $stmt->execute();

        return $user;
    }

    public function updateUser(User $user): User
    {
        $sql = 'UPDATE tb_usuarios SET nome = :name, email = :email, senha = :password, perfil_id = :perfil_id,
                        status = :status, updated_at = :updated_at, version = version + 1
                WHERE id = :id AND version = :expectedVersion';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':name', $user->getNome());
        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':password', $user->getSenha()->hash());
        $stmt->bindValue(':id', $user->getId(), PDO::PARAM_STR);
        $stmt->bindValue(':perfil_id', $user->getPerfil()->value);
        $stmt->bindValue(':status', $user->getStatus()->value);
        $stmt->bindValue(':updated_at', (new \DateTimeImmutable())->format('Y-m-d H:i:s'));
        $stmt->bindValue(':expectedVersion', $user->getVersion(), PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw UsuarioException::conflitoDeEdicao();
        }

        return $user;
    }

    public function deleteUser(string $id): bool
    {
        $sql = 'DELETE FROM tb_usuarios WHERE id = :id';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function getAllUser(): array
    {
        $sql = 'SELECT * FROM tb_usuarios';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        foreach ($usersData as $row) {
            $users[] = $this->userArray($row);
        }
        return $users;
    }

    public function getUserById(string $id): ?User
    {
        $sql = 'SELECT * FROM tb_usuarios WHERE id = :id';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $this->userArray($row);
    }

    public function getUserByEmail(string $email): ?User
    {
        $sql = 'SELECT * FROM tb_usuarios WHERE email = :email';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $this->userArray($row);
    }

    public function getAllUsersPaginado(int $limit, int $start): array
    {
        $sql = 'SELECT * FROM tb_usuarios  order by id desc LIMIT :start, :limit';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->execute();
        $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($usersData as $row) {
            $users[] = $this->userArray($row);
        }
        return $users;
    }

    public function countUsers(): int
    {
        $sql = 'SELECT COUNT(*) as total FROM tb_usuarios';
        $stmt = $this->conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    private function userArray(array|false $row): ?User
    {
        if (! $row) {
            return null;
        }
        return new User(
            nome: $row['nome'],
            email: $row['email'],
            senha: Senha::senhaHash($row['senha']),
            perfil: Perfil::from($row['perfil_id']),
            status: Status::from($row['status']),
            createdAt: new \DateTimeImmutable($row['created_at']),
            updatedAt: $row['updated_at'] ? new \DateTimeImmutable($row['updated_at']) : null,
            id: $row['id'],
            version: (int) $row['version'],
        );
    }
}
