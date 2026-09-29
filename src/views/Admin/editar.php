<?php

use Src\App\Utils\Url;

$user = $user ?? null;

$criando = $user === null;
$formAction = $criando ? Url::path('/admin/novo') : Url::path('/admin/editar/' . ($user?->getId() ?? ''));
$titulo = $criando ? 'Novo Usuário' : 'Editar Usuário';
$subtitulo = $criando ? 'Preencha os dados para criar um novo usuário' : 'Altere os campos desejados';
$userNome = $user?->getNome() ?? '';
$userEmail = $user?->getEmail() ?? '';
$userIsAdmin = $user?->isAdmin() ?? false;
$userIsAtivo = $user?->isAtivo() ?? false;
$isProprioUsuario = $user !== null && ($_SESSION['user_id'] ?? null) === $user->getId();
?>

<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <a href="<?= Url::path('/admin') ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:text-slate-900">
            <i class="fa-solid fa-arrow-left"></i>
            Voltar para Administração
        </a>
        <div>
            <h1 class="text-3xl italic font-medium tracking-tight  text-[var(--color-primary)] font-serif"><?= $titulo ?></h1>
            <p class="mt-1 text-sm text-slate-600"><?= $subtitulo ?></p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <?php if (!$criando && $user !== null) : ?>
            <div class="mb-5 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-sm font-semibold text-slate-700">
                    <?= htmlspecialchars(strtoupper(mb_substr($userNome, 0, 1))) ?>
                </div>
                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold text-slate-900"><?= htmlspecialchars($userNome) ?></div>
                    <div class="truncate text-sm text-slate-600"><?= htmlspecialchars($userEmail) ?></div>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $formAction ?>" class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <?= csrf() ?>
            <?php if (! $criando) : ?>
                <input type="hidden" name="version" value="<?= $user?->getVersion() ?? 1 ?>">
            <?php endif; ?>

            <div class="md:col-span-1">
                <label for="nome" class="text-sm font-medium text-slate-700">Nome</label>
                <input type="text" id="nome" name="nome" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100" value="<?= htmlspecialchars($userNome) ?>" placeholder="Nome completo" required>
                <?php $nomeErr = old_error('nome'); ?>
                <?php if ($nomeErr !== '') { ?>
                    <p class="mt-2 text-sm font-medium text-rose-700" id="nome-error"><?php echo htmlspecialchars($nomeErr); ?></p>
                <?php } ?>
            </div>

            <div class="md:col-span-1">
                <label for="email" class="text-sm font-medium text-slate-700">Email</label>
                <input type="email" id="email" name="email" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100" value="<?= htmlspecialchars($userEmail) ?>" placeholder="email@exemplo.com" required>
                <?php $emailErr = old_error('email'); ?>
                <?php if ($emailErr !== '') { ?>
                    <p class="mt-2 text-sm font-medium text-rose-700" id="email-error"><?php echo htmlspecialchars($emailErr); ?></p>
                <?php } ?>
            </div>

            <div class="md:col-span-2">
                <div class="flex items-center justify-between gap-2">
                    <label for="senha" class="text-sm font-medium text-slate-700">
                        <?= $criando ? 'Senha' : 'Nova senha' ?>
                        <?= $criando ? '<span class="text-rose-700">*</span>' : '' ?>
                    </label>
                    <?php if (! $criando) : ?>
                        <span class="text-sm text-slate-600">(deixe em branco para manter)</span>
                    <?php endif; ?>
                </div>

                <?php $senhaErr = old_error('senha'); ?>
                <?php if ($senhaErr !== '') { ?>
                    <p class="mt-2 text-sm font-medium text-rose-700" id="senha-error"><?php echo htmlspecialchars($senhaErr); ?></p>
                <?php } ?>

                <div class="mt-2 relative items-center gap-2">
                    <input type="password" id="senha" name="senha" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100" placeholder="<?= $criando ? 'Mínimo 8 caracteres, maiúscula, número e especial' : 'Nova senha...' ?>" <?= $criando ? 'required' : '' ?>>
                    <button type="button" class="toggle-password inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-700" data-target="senha" title="Ver senha">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <ul id="senhaRequisitos" class="password-requirements mt-3 flex-col gap-1" style="display:none;"></ul>
            </div>

            <div class="md:col-span-1">
                <label for="perfil" class="text-sm font-medium text-slate-700">Perfil</label>
                <select id="perfil" name="perfil" class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100">
                    <option value="1" <?= $userIsAdmin ? '' : 'selected' ?>>Usuário</option>
                    <option value="2" <?= $userIsAdmin ? 'selected' : '' ?>>Administrador</option>
                </select>
                <?php $perfilErr = old_error('perfil'); ?>
                <?php if ($perfilErr !== '') { ?>
                    <p class="mt-2 text-sm font-medium text-rose-700" id="perfil-error"><?php echo htmlspecialchars($perfilErr); ?></p>
                <?php } ?>
            </div>

            <?php if (!$criando) : ?>
                <div class="md:col-span-1">
                    <label for="status" class="text-sm font-medium text-slate-700">Status</label>
                    <select id="status" name="status" <?= $isProprioUsuario ? 'disabled' : '' ?> class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-500">
                        <option value="0" <?= $userIsAtivo ? '' : 'selected' ?> <?= $isProprioUsuario ? 'disabled' : '' ?>>Inativo</option>
                        <option value="1" <?= $userIsAtivo ? 'selected' : '' ?>>Ativo</option>
                    </select>
                    <?php if ($isProprioUsuario) : ?>
                        <input type="hidden" name="status" value="1">
                        <p class="mt-2 text-xs text-slate-500">Você não pode desativar a sua própria conta.</p>
                    <?php endif; ?>
                    <?php $statusErr = old_error('status'); ?>
                    <?php if ($statusErr !== '') { ?>
                        <p class="mt-2 text-sm font-medium text-rose-700" id="status-error"><?php echo htmlspecialchars($statusErr); ?></p>
                    <?php } ?>
                </div>
            <?php endif; ?>

            <div class="md:col-span-2 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <a href="<?= Url::path('/admin') ?>" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fa-solid fa-xmark"></i>
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    <i class="fa-solid fa-<?= $criando ? 'user-plus' : 'check' ?>"></i>
                    <?= $criando ? 'Criar usuário' : 'Salvar alterações' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script type="module" src="<?= Url::path('/js/admin-editar.js') ?>"></script>
