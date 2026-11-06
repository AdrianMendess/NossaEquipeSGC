<?php

/**
 * @var \Src\App\Models\Equipe $item
 * @var string $voltarUrl
 * @var string $editarUrl
 */

use Src\App\Utils\Url;

$cargosMap = [
    'dev_frontend'   => 'Desenvolvedor Front-end',
    'dev_backend'    => 'Desenvolvedor Back-end',
    'dev_fullstack'  => 'Desenvolvedor Full Stack',
    'infraestrutura' => 'Infraestrutura / DevOps',
    'analista_dados' => 'Analista de Dados',
];

$createdAt = $item->getCreatedAt() ? date('d/m/Y \à\s H:i', strtotime($item->getCreatedAt())) : '—';
$updatedAt = $item->getUpdatedAt() ? date('d/m/Y \à\s H:i', strtotime($item->getUpdatedAt())) : '—';
?>

<div class="max-w-3xl mx-auto space-y-6">
    <!-- Cabeçalho de Ações -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-900">Detalhes do Integrante</h1>
            <p class="mt-1 text-sm text-slate-600">Informações cadastradas no sistema</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= $voltarUrl ?>"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
            <a href="<?= $editarUrl . '?id=' . $item->getId() ?>"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition-colors shadow-sm">
                <i class="fa-solid fa-pen"></i> Editar
            </a>
        </div>
    </div>

    <!-- Card de Detalhes -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-6">
        <div class="flex items-center gap-5 border-b border-slate-100 pb-6">
            <?php if ($item->getFoto()) : ?>
                <img src="<?= Url::path('/' . htmlspecialchars($item->getFoto())) ?>"
                    alt="<?= htmlspecialchars($item->getNome()) ?>"
                    class="h-20 w-20 rounded-full object-cover border border-slate-200 shadow-sm">
            <?php else : ?>
                <div class="h-20 w-20 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200">
                    <i class="fa-solid fa-user text-3xl"></i>
                </div>
            <?php endif; ?>

            <div>
                <h2 class="text-2xl font-bold text-slate-900"><?= htmlspecialchars($item->getNome()) ?></h2>
                <p class="text-sm font-medium text-slate-500 mt-0.5"><?= htmlspecialchars($cargosMap[$item->getCargo()] ?? $item->getCargo() ?? '—') ?></p>
                <div class="mt-2">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                        <?= $item->getStatus() ? 'bg-green-100 text-green-700' : 'bg-rose-100 text-rose-700' ?>">
                        <?= $item->getStatus() ? 'Publicado' : 'Não publicado' ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block mb-1">Cadastrado em</span>
                <span class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fa-regular fa-calendar-plus text-slate-400"></i>
                    <?= $createdAt ?>
                </span>
            </div>

            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block mb-1">Última Atualização</span>
                <span class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fa-regular fa-clock text-slate-400"></i>
                    <?= $updatedAt ?>
                </span>
            </div>
        </div>
    </div>
</div>