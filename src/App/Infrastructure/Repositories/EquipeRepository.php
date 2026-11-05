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
        $stmt->bindValue(':status', $equipe->getStatus() ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':updated_at', $equipe->getUpdatedAt());
        $stmt->bindValue(':id', $equipe->getId(), PDO::PARAM_STR);
        $stmt->bindValue(':expectedVersion', $equipe->getVersion(), PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw EquipeException::conflitoDeEdicao();
        }

        return $equipe;
    }

public function getAll(?string $busca = null, string $ordem = 'ASC'): array
{
    $dir = strtoupper($ordem) === 'DESC' ? 'DESC' : 'ASC';
    
    $sql = 'SELECT id, foto, nome, cargo, status, version FROM tb_equipe';
    $params = [];

    if ($busca !== null && trim($busca) !== '') {
        $termo = trim($busca);
        
        // Mapeamento inverso: Texto amigável -> Slug salvo no banco
        $mapCargos = [
            'dev_frontend'   => ['desenvolvedor front-end', 'front-end', 'front', 'frontend'],
            'dev_backend'    => ['desenvolvedor back-end', 'back-end', 'back', 'backend'],
            'dev_fullstack'  => ['desenvolvedor full stack', 'full stack', 'fullstack'],
            'infraestrutura' => ['profissional de infraestrutura', 'infraestrutura', 'devops', 'infra'],
            'analista_dados' => ['analista de dados', 'dados', 'data'],
        ];

        $cargosEncontrados = [];
        $termoLower = mb_strtolower($termo);

        foreach ($mapCargos as $chave => $variacoes) {
            foreach ($variacoes as $var) {
                if (str_contains($var, $termoLower) || str_contains($termoLower, $var)) {
                    $cargosEncontrados[] = $chave;
                    break;
                }
            }
        }

        // Monta o SQL dinâmico
        $sql .= ' WHERE (nome LIKE :buscaNome OR cargo LIKE :buscaCargo';
        $params[':buscaNome']  = '%' . $termo . '%';
        $params[':buscaCargo'] = '%' . $termo . '%';

        // Se encontrou slugs correspondentes ao termo digitado, inclui no WHERE
        if (!empty($cargosEncontrados)) {
            $inClause = [];
            foreach ($cargosEncontrados as $i => $slug) {
                $paramName = ":slugCg{$i}";
                $inClause[] = $paramName;
                $params[$paramName] = $slug;
            }
            $sql .= ' OR cargo IN (' . implode(',', $inClause) . ')';
        }

        $sql .= ')';
    }

    $sql .= " ORDER BY created_at {$dir}";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute($params);
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
