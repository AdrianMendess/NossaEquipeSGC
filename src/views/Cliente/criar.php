<?php

/**
 * @var string $voltarUrl
 * @var string $urlSalvar
 */

?>

<div class="space-y-6">
    <div>
        <a href="<?= $voltarUrl ?>" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
        <h1 class="mt-2 text-2xl font-semibold text-slate-900">Novo Registro</h1>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <form method="POST" action="<?= $urlSalvar ?>">
            <?= csrf() ?>

            <div class="space-y-4">
                <div>
                    <label for="nome" class="block text-sm font-medium text-slate-700">Nome <span class="text-rose-600">*</span></label>
                    <input type="text" id="nome" name="nome" required minlength="3" maxlength="150"
                        value="<?= old('nome') ?>"
                        class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100">
                    <?= old_error('nome') ?>
                </div>

                <div>
                    <label for="tipo" class="block text-sm font-medium text-slate-700">Tipo <span class="text-rose-600">*</span></label>
                    <select id="tipo" name="tipo" required
                        class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100 bg-white">
                        <option value="" disabled <?= old('tipo') ? '' : 'selected' ?>>Selecione o tipo...</option>
                        <option value="pessoa_fisica" <?= old('tipo') === 'pessoa_fisica' ? 'selected' : '' ?>>Pessoa Física</option>
                        <option value="pessoa_juridica" <?= old('tipo') === 'pessoa_juridica' ? 'selected' : '' ?>>Pessoa Jurídica</option>
                    </select>
                    <?= old_error('tipo') ?>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="<?= $voltarUrl ?>"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    <i class="fa-solid fa-floppy-disk"></i> Salvar
                </button>
            </div>
        </form>
    </div>
</div>
