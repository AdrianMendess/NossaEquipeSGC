<?php

declare(strict_types=1);

namespace Src\App\Http\Controllers;

use Src\App\Dto\Equipe\CriarEquipeDto;
use Src\App\Dto\Equipe\EditEquipeDto;
use Src\App\Http\Exceptions\Equipe\EquipeException;
use Src\App\Http\Requests\EquipeRequest;
use Src\App\Services\IServices\IEquipeService;
use Src\App\Utils\Toast;
use Src\App\Utils\Url;
use Src\App\Utils\View;
use Src\Core\Logger;

// TODO: renomeie para o seu domínio e ajuste as rotas/títulos
class EquipeController extends SharedController
{
    public function __construct(
        private IEquipeService $equipeService
    ) {
    }

    public function index(): string
    {
        try {
            $itens = $this->equipeService->getAll();
            $content = View::render('Equipe/index', [
                'itens'      => $itens,
                'voltarUrl'  => Url::path('/home'),
                'urlCriar'   => Url::path('/equipe/criar'),
                'editarUrl'  => Url::path('/equipe/editar'),
                'deletarUrl' => Url::path('/equipe/deletar'),
            ]);

            return self::getPage('EXEMPLO - LISTAGEM', $content, [
                'showSidebar' => true,
                'bodyClass'   => 'equipe-page',
                'activePage'  => 'equipe',
            ]);
        } catch (EquipeException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/home');
        }
    }

    public function criar(): string
    {
        $content = View::render('Equipe/criar', [
            'voltarUrl' => Url::path('/equipe'),
            'urlSalvar' => Url::path('/equipe/salvar'),
        ]);

        return self::getPage('EXEMPLO - NOVO REGISTRO', $content, [
            'showSidebar' => true,
            'bodyClass'   => 'equipe-page',
            'activePage'  => 'equipe',
        ]);
    }

    public function editar(): string
    {
        $id = trim((string) filter_input(INPUT_GET, 'id'));

        try {
            $item = $this->equipeService->getById($id);

            $content = View::render('Equipe/editar', [
                'voltarUrl' => Url::path('/equipe'),
                'urlSalvar' => Url::path('/equipe/atualizar'),
                'item'      => $item,
            ]);

            return self::getPage('EXEMPLO - EDITAR REGISTRO', $content, [
                'showSidebar' => true,
                'bodyClass'   => 'equipe-page',
                'activePage'  => 'equipe',
            ]);
        } catch (EquipeException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/equipe');
        }
    }

    public function salvar(): never
    {
        try {
            $request = (new EquipeRequest($_POST))->redirectOnFail();
            $validated = $request->validated();

            $dto = CriarEquipeDto::fromArray($validated);
            $this->equipeService->create($dto);
            Toast::success('Registro criado com sucesso!');
            Url::redirect('/equipe');
        } catch (EquipeException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/equipe');
        }
    }

    public function atualizar(): void
    {
        try {
            $request = (new EquipeRequest($_POST))->redirectOnFail();
            $validated = $request->validated();

            $dto = EditEquipeDto::fromArray((string) $validated['id'], $validated);
            $this->equipeService->update($dto);
            Toast::success('Registro atualizado com sucesso!');
            Url::redirect('/equipe');
        } catch (EquipeException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/equipe/editar?id=' . ($_POST['id'] ?? ''));
        }
    }

    public function deletar(): void
    {
        $id = trim((string) filter_input(INPUT_POST, 'id'));

        try {
            $this->equipeService->delete($id);
            Toast::success('Registro deletado com sucesso!');
            Url::redirect('/equipe');
        } catch (EquipeException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/equipe');
        }
    }
}
