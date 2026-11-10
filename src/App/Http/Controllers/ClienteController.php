<?php

declare(strict_types=1);

namespace Src\App\Http\Controllers;

use Src\App\Dto\Cliente\CriarClienteDto;
use Src\App\Dto\Cliente\EditClienteDto;
use Src\App\Http\Exceptions\Cliente\ClienteException;
use Src\App\Http\Requests\ClienteRequest;
use Src\App\Services\IServices\IClienteService;
use Src\App\Utils\Toast;
use Src\App\Utils\Url;
use Src\App\Utils\View;
use Src\Core\Logger;

// TODO: renomeie para o seu domínio e ajuste as rotas/títulos
class ClienteController extends SharedController
{
    public function __construct(
        private IClienteService $clienteService
    ) {
    }

    public function index(): string
    {
        try {
            $itens = $this->clienteService->getAll();
            $content = View::render('Cliente/index', [
                'itens'      => $itens,
                'voltarUrl'  => Url::path('/home'),
                'urlCriar'   => Url::path('/cliente/criar'),
                'editarUrl'  => Url::path('/cliente/editar'),
                'deletarUrl' => Url::path('/cliente/deletar'),
            ]);

            return self::getPage('CLIENTE - LISTAGEM', $content, [
                'showSidebar' => true,
                'bodyClass'   => 'cliente-page',
                'activePage'  => 'cliente',
            ]);
        } catch (ClienteException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/home');
        }
    }

    public function criar(): string
    {
        $content = View::render('Cliente/criar', [
            'voltarUrl' => Url::path('/cliente'),
            'urlSalvar' => Url::path('/cliente/salvar'),
        ]);

        return self::getPage('CLIENTE - NOVO REGISTRO', $content, [
            'showSidebar' => true,
            'bodyClass'   => 'cliente-page',
            'activePage'  => 'cliente',
        ]);
    }

    public function editar(): string
    {
        $id = trim((string) filter_input(INPUT_GET, 'id'));

        try {
            $item = $this->clienteService->getById($id);

            $content = View::render('Cliente/editar', [
                'voltarUrl' => Url::path('/cliente'),
                'urlSalvar' => Url::path('/cliente/atualizar'),
                'item'      => $item,
            ]);

            return self::getPage('CLIENTE - EDITAR REGISTRO', $content, [
                'showSidebar' => true,
                'bodyClass'   => 'cliente-page',
                'activePage'  => 'cliente',
            ]);
        } catch (ClienteException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/cliente');
        }
    }

    public function salvar(): never
    {
        try {
            $request = (new ClienteRequest($_POST))->redirectOnFail();
            $validated = $request->validated();

            $dto = CriarClienteDto::fromArray($validated);
            $this->clienteService->create($dto);
            Toast::success('Registro criado com sucesso!');
            Url::redirect('/cliente');
        } catch (ClienteException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/cliente');
        }
    }

    public function atualizar(): void
    {
        try {
            $request = (new ClienteRequest($_POST))->redirectOnFail();
            $validated = $request->validated();

            $dto = EditClienteDto::fromArray((string) $validated['id'], $validated);
            $this->clienteService->update($dto);
            Toast::success('Registro atualizado com sucesso!');
            Url::redirect('/cliente');
        } catch (ClienteException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/cliente/editar?id=' . ($_POST['id'] ?? ''));
        }
    }

    public function deletar(): void
    {
        $id = trim((string) filter_input(INPUT_POST, 'id'));

        try {
            $this->clienteService->delete($id);
            Toast::success('Registro deletado com sucesso!');
            Url::redirect('/cliente');
        } catch (ClienteException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/cliente');
        }
    }
}
