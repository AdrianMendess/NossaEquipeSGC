<?php

/**
 * @var string  $voltarUrl
 * @var string  $urlSalvar
 * @var \Src\App\Models\Cliente $item
 */

$nomeAtual   = $item ? $item->getNome() : '';
$tipoAtual   = old('tipo') ?: ($item ? $item->getTipo() : '');
$statusAtual = ($item && $item->getStatus()) ? '1' : '0';

?>

<div class="space-y-6">
    <div>
        <a href="<?= $voltarUrl ?>" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
        <h1 class="mt-2 text-2xl font-semibold text-slate-900">Editar Registro</h1>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <form method="POST" action="<?= $urlSalvar ?>">
            <?= csrf() ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($item->getId()) ?>">
            <input type="hidden" name="version" value="<?= $item->getVersion() ?>">

            <div class="space-y-4">
                <div>
                    <label for="nome" class="block text-sm font-medium text-slate-700">Nome <span class="text-rose-600">*</span></label>
                    <input type="text" id="nome" name="nome" required minlength="3" maxlength="150"
                        value="<?= htmlspecialchars($item->getNome()) ?>"
                        class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100">
                    <?= old_error('nome') ?>
                </div>

                <div>
                    <label for="tipo" class="block text-sm font-medium text-slate-700">Tipo <span class="text-rose-600">*</span></label>
                    <select id="tipo" name="tipo" required
                        class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100 bg-white">
                        <option value="" disabled <?= empty($tipoAtual) ? 'selected' : '' ?>>Selecione o tipo...</option>
                        <option value="pessoa_fisica" <?= $tipoAtual === 'pessoa_fisica' ? 'selected' : '' ?>>Pessoa Física</option>
                        <option value="pessoa_juridica" <?= $tipoAtual === 'pessoa_juridica' ? 'selected' : '' ?>>Pessoa Jurídica</option>
                    </select>
                    <?= old_error('tipo') ?>
                </div>
                <div>
                    <label for="cpf_cnpj" class="block text-sm font-medium text-slate-700">CPF / CNPJ</label>
                    <input type="text" id="cpf_cnpj" name="cpf_cnpj" maxlength="18"
                        value="<?= htmlspecialchars((string) (old('cpf_cnpj') ?: ($item->getCpfCnpj() ?? ''))) ?>"
                        placeholder="Apenas números ou formatado"
                        class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100">
                    <?= old_error('cpf_cnpj') ?>
                </div>

                <div>
                    <label for="telefone" class="block text-sm font-medium text-slate-700">Telefone</label>
                    <input type="text" id="telefone" name="telefone" maxlength="15"
                        value="<?= htmlspecialchars((string) (old('telefone') ?: ($item->getTelefone() ?? ''))) ?>"
                        placeholder="DDD + número"
                        class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100">
                    <?= old_error('telefone') ?>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                    <select id="status" name="status" class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white">
                        <option value="1" <?= (string) $statusAtual === '1' ? 'selected' : '' ?>>Ativo</option>
                        <option value="0" <?= $statusAtual === '0' ? 'selected' : '' ?>>Inativo</option>
                    </select>
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
    <script type="module" src="<?= \Src\App\Utils\Url::base() ?>/js/cliente/form.js"></script>
</div>