<?php

/**
 * @var array  $itens
 * @var string $urlCriar
 * @var string $editarUrl
 * @var string $deletarUrl
 * @var string $urlIndex
 * @var string $ordem
 * @var string $busca
 */

use Src\App\Utils\Url;

$cargosMap = [
    'dev_frontend'   => 'Desenvolvedor Front-end',
    'dev_backend'    => 'Desenvolvedor Back-end',
    'dev_fullstack'  => 'Desenvolvedor Full Stack',
    'infraestrutura' => 'Infraestrutura / DevOps',
    'analista_dados' => 'Analista de Dados',
];

$busca = $busca ?? '';
$ordem = $ordem ?? 'ASC';
$urlIndex = $urlIndex ?? Url::path('/equipe');
$proximaOrdem = ($ordem === 'ASC') ? 'DESC' : 'ASC';
$textoOrdem = ($ordem === 'ASC') ? 'Ordem (ASC)' : 'Ordem (DESC)';
?>

<div class="space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-900">Equipe</h1>
            <p class="mt-1 text-sm text-slate-600">Gerencie os integrantes da equipe</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Botão de Alternar Ordem (preserva a busca caso exista) -->
            <a href="<?= $urlIndex ?>?ordem=<?= $proximaOrdem ?><?= !empty($busca) ? '&busca=' . urlencode($busca) : '' ?>"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm"
                title="Clique para alternar para <?= $proximaOrdem === 'ASC' ? 'ASC' : 'DESC' ?>">
                <span><?= $textoOrdem ?></span>
            </a>

            <a href="<?= $urlCriar ?>"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                <i class="fa-solid fa-plus"></i>
                Novo Integrante
            </a>
        </div>
    </div>

    <!-- Formulário de Busca -->
    <form method="GET" action="<?= $urlIndex ?>" class="flex items-center gap-2 max-w-md">
        <input type="hidden" name="ordem" value="<?= htmlspecialchars($ordem) ?>">
        <div class="relative w-full flex items-center">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input 
                type="text" 
                name="busca" 
                value="<?= htmlspecialchars($busca) ?>" 
                placeholder="Buscar por nome ou cargo..." 
                class="w-full rounded-xl border border-slate-200 bg-white pl-11 pr-3 py-2 text-sm text-slate-900 focus:border-slate-400 focus:outline-none"
            />
        </div>
        <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Buscar
        </button>
        <?php if (!empty($busca)): ?>
            <a href="<?= $urlIndex ?>?ordem=<?= $ordem ?>" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Limpar
            </a>
        <?php endif; ?>
    </form>

    <?php if (empty($itens)) : ?>
        <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-10 text-center">
            <div class="mx-auto inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                <i class="fa-solid fa-users"></i>
            </div>
            <p class="mt-3 text-sm font-semibold text-slate-900">
                <?= !empty($busca) ? 'Nenhum integrante encontrado.' : 'Nenhum integrante cadastrado.' ?>
            </p>
            <p class="mt-1 text-sm text-slate-600">
                <?= !empty($busca) ? 'Tente buscar por outros termos.' : 'Clique em "Novo Integrante" para começar.' ?>
            </p>
        </div>
    <?php else : ?>
        <div class="rounded-2xl border border-slate-200 bg-white">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="px-5 py-3 text-left font-semibold text-slate-700">Foto</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-700">Nome</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-700">Cargo</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-700">Status</th>
                        <th class="px-5 py-3 text-right font-semibold text-slate-700">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($itens as $item) : ?>
                        <tr>
                            <td class="px-5 py-3">
                                <?php if ($item->getFoto()) : ?>
                                    <img src="<?= Url::path('/' . htmlspecialchars($item->getFoto())) ?>" alt="<?= htmlspecialchars($item->getNome()) ?>" class="h-10 w-10 rounded-full object-cover border border-slate-200">
                                <?php else : ?>
                                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3 font-medium text-slate-900"><?= htmlspecialchars($item->getNome()) ?></td>
                            <td class="px-5 py-3 text-slate-600"><?= htmlspecialchars($cargosMap[$item->getCargo()] ?? $item->getCargo() ?? '—') ?></td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            <?= $item->getStatus() ? 'bg-green-100 text-green-700' : 'bg-rose-100 text-rose-700' ?>">
                                    <?= $item->getStatus() ? 'Publicado' : 'Não publicado' ?>
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= $editarUrl . '?id=' . $item->getId() ?>"
                                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        <i class="fa-solid fa-pen"></i> Editar
                                    </a>
                                    <button type="button"
                                        class="js-btn-excluir inline-flex items-center gap-1 rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700"
                                        data-id="<?= htmlspecialchars($item->getId()) ?>"
                                        data-nome="<?= htmlspecialchars($item->getNome()) ?>"
                                        data-url="<?= $deletarUrl ?>"
                                        data-campo="id">
                                        <i class="fa-solid fa-trash"></i> Excluir
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>