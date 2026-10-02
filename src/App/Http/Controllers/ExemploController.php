<?php

declare(strict_types=1);

namespace Src\App\Http\Controllers;

use Src\App\Dto\Exemplo\CriarExemploDto;
use Src\App\Dto\Exemplo\EditExemploDto;
use Src\App\Http\Exceptions\Exemplo\ExemploException;
use Src\App\Http\Requests\ExemploRequest;
use Src\App\Services\IServices\IExemploService;
use Src\App\Utils\Toast;
use Src\App\Utils\Url;
use Src\App\Utils\View;
use Src\Core\Logger;

// TODO: renomeie para o seu domínio e ajuste as rotas/títulos
class ExemploController extends SharedController
{
    public function __construct(
        private IExemploService $exemploService
    ) {
    }

    public function index(): string
    {
        try {
            $itens = $this->exemploService->getAll();
            $content = View::render('Exemplo/index', [
                'itens'      => $itens,
                'voltarUrl'  => Url::path('/home'),
                'urlCriar'   => Url::path('/exemplo/criar'),
                'editarUrl'  => Url::path('/exemplo/editar'),
                'deletarUrl' => Url::path('/exemplo/deletar'),
            ]);

            return self::getPage('EXEMPLO - LISTAGEM', $content, [
                'showSidebar' => true,
                'bodyClass'   => 'exemplo-page',
                'activePage'  => 'exemplo',
            ]);
        } catch (ExemploException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/home');
        }
    }

    public function criar(): string
    {
        $content = View::render('Exemplo/criar', [
            'voltarUrl' => Url::path('/exemplo'),
            'urlSalvar' => Url::path('/exemplo/salvar'),
        ]);

        return self::getPage('EXEMPLO - NOVO REGISTRO', $content, [
            'showSidebar' => true,
            'bodyClass'   => 'exemplo-page',
            'activePage'  => 'exemplo',
        ]);
    }

    public function editar(): string
    {
        $id = trim((string) filter_input(INPUT_GET, 'id'));

        try {
            $item = $this->exemploService->getById($id);

            $content = View::render('Exemplo/editar', [
                'voltarUrl' => Url::path('/exemplo'),
                'urlSalvar' => Url::path('/exemplo/atualizar'),
                'item'      => $item,
            ]);

            return self::getPage('EXEMPLO - EDITAR REGISTRO', $content, [
                'showSidebar' => true,
                'bodyClass'   => 'exemplo-page',
                'activePage'  => 'exemplo',
            ]);
        } catch (ExemploException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/exemplo');
        }
    }

    public function salvar(): never
    {
        try {
            $request = (new ExemploRequest($_POST))->redirectOnFail();
            $validated = $request->validated();

            $dto = CriarExemploDto::fromArray($validated);
            $this->exemploService->create($dto);
            Toast::success('Registro criado com sucesso!');
            Url::redirect('/exemplo');
        } catch (ExemploException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/exemplo');
        }
    }

    public function atualizar(): void
    {
        try {
            $request = (new ExemploRequest($_POST))->redirectOnFail();
            $validated = $request->validated();

            $dto = EditExemploDto::fromArray((string) $validated['id'], $validated);
            $this->exemploService->update($dto);
            Toast::success('Registro atualizado com sucesso!');
            Url::redirect('/exemplo');
        } catch (ExemploException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/exemplo/editar?id=' . ($_POST['id'] ?? ''));
        }
    }

    public function deletar(): void
    {
        $id = trim((string) filter_input(INPUT_POST, 'id'));

        try {
            $this->exemploService->delete($id);
            Toast::success('Registro deletado com sucesso!');
            Url::redirect('/exemplo');
        } catch (ExemploException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/exemplo');
        }
    }
}
