# Como criar um novo módulo

Este guia mostra passo a passo como adicionar um módulo ao projeto usando o módulo `Exemplo` como referência.
Substitua `Exemplo`/`exemplo` pelo nome do seu domínio (ex: `Produto`, `Cliente`, `Pedido`).

---

## 1. Model — `src/App/Models/Produto.php`

Representa a entidade de domínio. Use getters/setters, sem lógica de negócio.

```php
namespace Src\App\Models;

class Produto
{
    public function __construct(
        private string $nome,
        private ?float $preco = null,
        private ?bool $status = null,
        private ?string $created_at = null,
        private ?string $updated_at = null,
        private ?int $id = null
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function getPreco(): ?float { return $this->preco; }
    public function getStatus(): ?bool { return $this->status; }
    public function getCreatedAt(): ?string { return $this->created_at; }
    public function getUpdatedAt(): ?string { return $this->updated_at; }
    // setters...
}
```

---

## 2. Exception — `src/App/Http/Exceptions/Produto/ProdutoException.php`

```php
namespace Src\App\Http\Exceptions\Produto;

use Src\App\Http\Exceptions\DomainException;

class ProdutoException extends DomainException
{
    public static function naoEncontrado(): self
    {
        return new self('Produto não encontrado.');
    }
    // erroCriar(), erroAtualizar(), erroDeletar()...
}
```

---

## 3. Interface do Repositório — `src/App/Infrastructure/IRepositories/IProdutoRepository.php`

```php
namespace Src\App\Infrastructure\IRepositories;

use Src\App\Models\Produto;

interface IProdutoRepository
{
    public function create(Produto $produto): Produto;
    public function update(Produto $produto): Produto;
    public function delete(int $id): bool;
    public function getAll(): array;
    public function getById(int $id): ?Produto;
}
```

---

## 4. Repositório — `src/App/Infrastructure/Repositories/ProdutoRepository.php`

Implementa a interface com SQL direto via PDO. Siga o padrão de `ExemploRepository`:
- `create()` → INSERT, retorna objeto com ID do `lastInsertId()`
- `update()` → UPDATE, retorna o objeto atualizado
- `getAll()` → SELECT, retorna array de objetos
- `getById()` → SELECT WHERE id, retorna objeto ou null
- `delete()` → DELETE WHERE id, retorna bool

---

## 5. Interface do Service — `src/App/Services/IServices/IProdutoService.php`

```php
namespace Src\App\Services\IServices;

use Src\App\Models\Produto;

interface IProdutoService
{
    public function create(string $nome, float $preco): Produto;
    public function update(int $id, string $nome, float $preco): Produto;
    public function delete(int $id): bool;
    public function getAll(): array;
    public function getById(int $id): ?Produto;
}
```

---

## 6. Service — `src/App/Services/ProdutoService.php`

Contém as regras de negócio. Recebe o repositório via construtor (injeção de dependência).

```php
namespace Src\App\Services;

use Src\App\Services\IServices\IProdutoService;
use Src\App\Infrastructure\IRepositories\IProdutoRepository;
use Src\App\Models\Produto;

class ProdutoService implements IProdutoService
{
    public function __construct(private IProdutoRepository $repository) {}

    public function create(string $nome, float $preco): Produto
    {
        $agora = date('Y-m-d H:i:s');
        return $this->repository->create(new Produto($nome, $preco, true, $agora, $agora));
    }
    // update(), delete(), getAll(), getById()...
}
```

---

## 7. Request — `src/App/Http/Requests/ProdutoRequest.php`

Define as regras de validação do formulário. Estende `Request`.

```php
namespace Src\App\Http\Requests;

use Src\Core\Request;

class ProdutoRequest extends Request
{
    public function rules(): array
    {
        return [
            'nome'  => ['required', 'string', 'min:3', 'max:150'],
            'preco' => ['required', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'  => 'O nome é obrigatório.',
            'preco.required' => 'O preço é obrigatório.',
            'preco.numeric'  => 'O preço deve ser um número.',
        ];
    }
}
```

**Regras disponíveis:** `required`, `nullable`, `string`, `numeric`, `email`, `min:N`, `max:N`,
`regex:/padrão/`, `fileType:ext1,ext2`, `fileSize:KB`, `unique:tabela,coluna`, `exists:tabela,coluna`

---

## 8. Controller — `src/App/Http/Controllers/ProdutoController.php`

Orquestra Request → Service → View. Nunca acessa o repositório diretamente.

```php
namespace Src\App\Http\Controllers;

use Src\App\Http\Exceptions\Produto\ProdutoException;
use Src\App\Http\Requests\ProdutoRequest;
use Src\App\Services\IServices\IProdutoService;
use Src\App\Utils\{Toast, Url, View};
use Src\Core\Logger;

class ProdutoController extends SharedController
{
    public function __construct(private IProdutoService $produtoService) {}

    public function index(): string
    {
        $itens = $this->produtoService->getAll();
        $content = View::render('Produto/index', ['itens' => $itens]);
        return self::getPage('Produtos', $content, ['activePage' => 'produto']);
    }

    public function salvar(): never
    {
        $validated = (new ProdutoRequest($_POST))->redirectOnFail()->validated();
        $this->produtotService->create($validated['nome'], (float) $validated['preco']);
        Toast::success('Produto criado!');
        Url::redirect('/produto');
    }
    // criar(), editar(), atualizar(), deletar()...
}
```

---

## 9. Rotas — `src/routes/modulos/produto.php`

**Descoberto automaticamente** via `glob()`. Basta criar o arquivo.

```php
use Src\App\Http\Controllers\ProdutoController;
use Src\App\Http\Middleware\AuthMiddleware;

$router->get('/produto',          [ProdutoController::class, 'index'],    [AuthMiddleware::class]);
$router->get('/produto/criar',    [ProdutoController::class, 'criar'],    [AuthMiddleware::class]);
$router->get('/produto/editar',   [ProdutoController::class, 'editar'],   [AuthMiddleware::class]);
$router->post('/produto/salvar',  [ProdutoController::class, 'salvar'],   [AuthMiddleware::class]);
$router->post('/produto/atualizar',[ProdutoController::class, 'atualizar'],[AuthMiddleware::class]);
$router->post('/produto/deletar', [ProdutoController::class, 'deletar'],  [AuthMiddleware::class]);
```

**Middlewares disponíveis:** `AuthMiddleware` (requer login), `AdminMiddleware` (requer perfil ADMIN),
`GuestMiddleware` (apenas não-autenticados).

---

## 10. DI Config — `src/config/dependencies/modulos/produto.php`

**Descoberto automaticamente** via `glob()`. Registra as ligações interface → implementação.

```php
use Src\App\Http\Controllers\ProdutoController;
use Src\App\Infrastructure\IRepositories\IProdutoRepository;
use Src\App\Infrastructure\Repositories\ProdutoRepository;
use Src\App\Services\ProdutoService;
use Src\App\Services\IServices\IProdutoService;
use Src\Core\{Container, Database};

Container::set(IProdutoRepository::class, fn() =>
    new ProdutoRepository(Container::get(Database::class))
);

Container::set(IProdutoService::class, fn() =>
    new ProdutoService(Container::get(IProdutoRepository::class))
);

Container::set(ProdutoController::class, fn() =>
    new ProdutoController(Container::get(IProdutoService::class))
);
```

---

## 11. Views — `src/views/Produto/`

Crie `index.php`, `criar.php` e `editar.php`. Baseie-se no módulo `Exemplo`.

Helpers disponíveis nas views:
- `csrf()` — insere o token CSRF oculto
- `old('campo')` — repopula o campo após erro de validação
- `old_error('campo')` — exibe a mensagem de erro do campo
- `Url::path('/rota')` — gera URL com o base path correto

---

## 12. Banco de dados

Adicione a tabela ao seu arquivo SQL de migração:

```sql
CREATE TABLE `tb_produto` (
  `id`         int NOT NULL AUTO_INCREMENT,
  `nome`       varchar(150) NOT NULL,
  `preco`      decimal(10,2) NOT NULL,
  `status`     tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 13. Menu lateral

Em `src/views/Shared/sidebar.php`, adicione o link do novo módulo:

```php
<li>
    <a href="<?= htmlspecialchars($urlProduto) ?>"
        class="<?= $activeProduto ? $linkActive : $linkInactive ?>">
        <i class="fa-solid fa-box text-base"></i>
        <span>Produtos</span>
    </a>
</li>
```

E em `src/App/Http/Controllers/SharedController.php`, registre a variável no `getSidebar()`:

```php
'activeProduto' => $activePage === 'produto',
'urlProduto'    => Url::path('/produto'),
```

---

## Checklist de um módulo completo

- [ ] `Model` com getters/setters
- [ ] `Exception` com métodos estáticos descritivos
- [ ] `IRepository` com assinatura dos métodos
- [ ] `Repository` implementando a interface (SQL com PDO)
- [ ] `IService` com assinatura dos métodos
- [ ] `Service` com regras de negócio
- [ ] `Request` com regras de validação
- [ ] `Controller` orquestrando Request → Service → View
- [ ] `routes/modulos/modulo.php`
- [ ] `config/dependencies/modulos/modulo.php`
- [ ] `views/Modulo/` com index, criar e editar
- [ ] Tabela SQL adicionada
- [ ] Link no sidebar
