<?php

/**
 * @var string  $voltarUrl
 * @var string  $urlSalvar
 * @var \Src\App\Models\Equipe $item
 */

use Src\App\Utils\Url;

$cargoAtual = old('cargo', $item->getCargo());
?>

<div class="space-y-6">
    <div>
        <a href="<?= $voltarUrl ?>" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
        <h1 class="mt-2 text-2xl font-semibold text-slate-900">Editar Integrante</h1>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <form method="POST" action="<?= $urlSalvar ?>" enctype="multipart/form-data">
            <?= csrf() ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($item->getId()) ?>">
            <input type="hidden" name="version" value="<?= $item->getVersion() ?>">

            <div class="space-y-4">
                <div>
                    <label for="foto" class="block text-sm font-medium text-slate-700">Foto</label>
                    <?php if ($item->getFoto()) : ?>
                        <div class="my-2 flex items-center gap-3">
                            <img src="<?= Url::path('/' . htmlspecialchars($item->getFoto())) ?>" alt="Foto atual" class="h-14 w-14 rounded-full object-cover border border-slate-200">
                            <span class="text-xs text-slate-500">Deixe em branco para manter a foto atual</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="foto" name="foto" accept="image/*"
                        class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    <?= old_error('foto') ?>
                </div>

                <div>
                    <label for="nome" class="block text-sm font-medium text-slate-700">Nome <span class="text-rose-600">*</span></label>
                    <input type="text" id="nome" name="nome" required minlength="3" maxlength="150"
                        value="<?= old('nome', htmlspecialchars($item->getNome())) ?>"
                        class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100">
                    <?= old_error('nome') ?>
                </div>

                <div>
                    <label for="cargo" class="block text-sm font-medium text-slate-700">Cargo <span class="text-rose-600">*</span></label>
                    <select id="cargo" name="cargo" required
                        class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100 bg-white">
                        <option value="" disabled <?= empty($cargoAtual) ? 'selected' : '' ?>>Selecione o cargo...</option>
                        <option value="dev_frontend" <?= $cargoAtual === 'dev_frontend' ? 'selected' : '' ?>>Desenvolvedor Front-end</option>
                        <option value="dev_backend" <?= $cargoAtual === 'dev_backend' ? 'selected' : '' ?>>Desenvolvedor Back-end</option>
                        <option value="dev_fullstack" <?= $cargoAtual === 'dev_fullstack' ? 'selected' : '' ?>>Desenvolvedor Full Stack</option>
                        <option value="infraestrutura" <?= $cargoAtual === 'infraestrutura' ? 'selected' : '' ?>>Profissional de Infraestrutura / DevOps</option>
                        <option value="analista_dados" <?= $cargoAtual === 'analista_dados' ? 'selected' : '' ?>>Analista de Dados</option>
                    </select>
                    <?= old_error('cargo') ?>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="<?= $voltarUrl ?>"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    <i class="fa-solid fa-floppy-disk"></i> Atualizar
                </button>
            </div>
        </form>
    </div>
</div>
