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
use Src\Core\Storage;

class EquipeController extends SharedController
{
    public function __construct(
        private IEquipeService $equipeService
    ) {
    }

    public function index(): string
    {
        try {
            $busca = trim((string) filter_input(INPUT_GET, 'busca'));
            $ordem = strtoupper((string) (filter_input(INPUT_GET, 'ordem') ?? 'ASC'));
            $ordem = in_array($ordem, ['ASC', 'DESC'], true) ? $ordem : 'ASC';

            $itens = $this->equipeService->getAll($busca, $ordem);
            $content = View::render('Equipe/index', [
                'itens'      => $itens,
                'busca'      => $busca,
                'ordem'      => $ordem,
                'urlIndex'   => Url::path('/equipe'),
                'voltarUrl'  => Url::path('/home'),
                'urlCriar'   => Url::path('/equipe/criar'),
                'editarUrl'  => Url::path('/equipe/editar'),
                'deletarUrl' => Url::path('/equipe/deletar'),
            ]);

            return self::getPage('EQUIPE - LISTAGEM', $content, [
                'showSidebar' => true,
                'bodyClass'   => 'equipe-page',
                'activePage'  => 'equipe',
            ]);
        } catch (EquipeException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/home');
        } catch (\Throwable $e) {
            Toast::error('Erro ao listar registros.');
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

        return self::getPage('EQUIPE - NOVO REGISTRO', $content, [
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

            return self::getPage('EQUIPE - EDITAR REGISTRO', $content, [
                'showSidebar' => true,
                'bodyClass'   => 'equipe-page',
                'activePage'  => 'equipe',
            ]);
        } catch (EquipeException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/equipe');
        } catch (\Throwable $e) {
            Toast::error('Erro ao buscar registro.');
            Logger::error($e->getMessage());
            Url::redirect('/equipe');
        }
    }

    public function salvar(): never
    {
        try {
            $request = (new EquipeRequest($_POST))->redirectOnFail();
            $validated = $request->validated();

            if (!empty($validated['foto']) && is_array($validated['foto'])) {
                $storage = new Storage();
                $caminho = $storage->put($validated['foto'], 'uploads/equipe');
                if (!$caminho) {
                    Toast::error('Erro ao processar o upload da foto.');
                    Url::redirect('/equipe/criar');
                }
                $validated['foto'] = $caminho;
            }

            $dto = CriarEquipeDto::fromArray($validated);
            $this->equipeService->create($dto);
            Toast::success('Registro criado com sucesso!');
            Url::redirect('/equipe');
        } catch (EquipeException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/equipe');
        } catch (\Throwable $e) {
            Toast::error('Erro inesperado: ' . $e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/equipe');
        }
    }

    public function atualizar(): void
    {
        try {
            $request = (new EquipeRequest($_POST))->redirectOnFail();
            $validated = $request->validated();

            if (!empty($validated['foto']) && is_array($validated['foto']) && ($validated['foto']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $storage = new Storage();
                $caminho = $storage->put($validated['foto'], 'uploads/equipe');
                if (!$caminho) {
                    Toast::error('Erro ao processar o upload da nova foto.');
                    Url::redirect('/equipe/editar?id=' . ($_POST['id'] ?? ''));
                }
                $validated['foto'] = $caminho;
            } else {
                $validated['foto'] = null;
            }

            $dto = EditEquipeDto::fromArray((string) $validated['id'], $validated);
            $this->equipeService->update($dto);
            Toast::success('Registro atualizado com sucesso!');
            Url::redirect('/equipe');
        } catch (EquipeException $e) {
            Toast::error($e->getMessage());
            Logger::error($e->getMessage());
            Url::redirect('/equipe/editar?id=' . ($_POST['id'] ?? ''));
        } catch (\Throwable $e) {
            Toast::error('Erro inesperado: ' . $e->getMessage());
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
        } catch (\Throwable $e) {
            Toast::error('Erro ao deletar registro.');
            Logger::error($e->getMessage());
            Url::redirect('/equipe');
        }
    }

    public function alternarStatus(): void
{
    $id = trim((string) filter_input(INPUT_GET, 'id'));

    try {
        $item = $this->equipeService->getById($id);

        if ($item) {
            // Inverte o status atual (se for true/1 vira false/0, e vice-versa)
            $novoStatus = !$item->getStatus();

            // Cria o DTO com o novo status mantendo os outros dados
            $dto = new EditEquipeDto(
                id: $item->getId(),
                nome: $item->getNome(),
                cargo: $item->getCargo(),
                foto: null, // Mantém a foto existente no Service
                version: $item->getVersion(),
                status: $novoStatus
            );

            $this->equipeService->update($dto);
            Toast::success('Status de publicação alterado com sucesso!');
        }
    } catch (\Throwable $e) {
        Toast::error('Erro ao alternar status do integrante.');
        Logger::error($e->getMessage());
    }

    // Redireciona de volta para a listagem
    Url::redirect('/equipe');
}
}
