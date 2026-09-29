<?php

use Src\App\Enums\Status;
use Src\App\Utils\Url;

$usuario = $usuario ?? null;
$urlAtualizarSenha = $urlAtualizarSenha ?? '';

?>

<?php if ($usuario !== null) : ?>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Configurações</h1>
            <p class="mt-1 text-sm text-slate-600">Gerencie sua conta e preferências</p>
        </div>

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 xl:col-span-1">
                <div class="flex items-center gap-3">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-base font-semibold text-slate-700">
                        <?= htmlspecialchars(strtoupper(mb_substr($usuario->getNome(), 0, 1))) ?>
                    </div>
                    <div class="min-w-0">
                        <div class="truncate text-base font-semibold text-slate-900"><?= htmlspecialchars($usuario->getNome()) ?></div>
                        <div class="truncate text-sm text-slate-600"><?= htmlspecialchars($usuario->getEmail()) ?></div>
                        <div class="mt-2">
                            <?php if ($usuario->isAdmin()) : ?>
                                <span class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                    <i class="fa-solid fa-shield-halved"></i>
                                    Admin
                                </span>
                            <?php else : ?>
                                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                    <i class="fa-solid fa-user"></i>
                                    Usuário
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="text-sm font-medium text-slate-600">ID</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">#<?= (string) $usuario->getId() ?></p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="text-sm font-medium text-slate-600">Status</p>
                        <div class="mt-2">
                            <?php if ($usuario->isAtivo()) : ?>
                                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                    <i class="fa-solid fa-circle text-[0.45rem]"></i>
                                    <?= $usuario->getStatus()->toText() ?>
                                </span>
                            <?php else : ?>
                                <span class="inline-flex items-center gap-2 rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                    <i class="fa-solid fa-circle text-[0.45rem]"></i>
                                    <?= $usuario->getStatus()->toText() ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="text-sm font-medium text-slate-600">Membro desde</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900"><?= $usuario->getCreatedAt() ? $usuario->getCreatedAt()->format('d/m/Y') : '-' ?></p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 xl:col-span-2">
                <div class="flex items-start gap-3">
                    <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Alterar senha</h2>
                        <p class="mt-1 text-sm text-slate-600">Atualize sua senha de acesso</p>
                    </div>
                </div>

                <form method="POST" action="<?= htmlspecialchars($urlAtualizarSenha) ?>" data-no-client-validation="true"
                    id="form-alterar-senha" class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <?= csrf() ?>

                    <div class="md:col-span-1">
                        <label for="senha_atual" class="text-sm font-medium text-slate-700">Senha atual</label>
                        <div class="mt-1 relative items-center gap-2">
                            <input type="password" id="senha_atual" name="senha_atual" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100" placeholder="Digite sua senha atual" required>
                            <button type="button" class="toggle-password inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-700" data-target="senha_atual" title="Ver senha">
                                <i class="fa-solid fa-eye-slash"></i>
                            </button>
                        </div>
                        <?php $senhaAtualErr = old_error('senha_atual'); ?>
                        <?php if ($senhaAtualErr !== '') { ?>
                            <p class="mt-2 text-sm font-medium text-rose-700" id="senha-error"><?php echo htmlspecialchars($senhaAtualErr); ?></p>
                        <?php } ?>
                    </div>

                    <div class="md:col-span-1">
                        <label for="nova_senha" class="text-sm font-medium text-slate-700">Nova senha</label>
                        <div class="mt-1 relative items-center gap-2">
                            <input type="password" id="nova_senha" name="nova_senha" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100" placeholder="Mínimo 8 caracteres, maiúscula, número e especial" minlength="8" required>
                            <button type="button" class="toggle-password absolute inset-y-0 right-0 flex items-center pr-3   text-slate-700 h" data-target="nova_senha" title="Ver senha">
                                <i class="fa-solid fa-eye-slash"></i>
                            </button>
                        </div>
                        <?php $novaSenhaErr = old_error('nova_senha'); ?>
                        <?php if ($novaSenhaErr !== '') { ?>
                            <p class="mt-2 text-sm font-medium text-rose-700" id="nova-senha-error"><?php echo htmlspecialchars($novaSenhaErr); ?></p>
                        <?php } ?>
                        <ul id="senhaRequisitos" class="password-requirements mt-3 flex-col gap-1" style="display:none;"></ul>
                    </div>

                    <div class="md:col-span-2">
                        <label for="confirmar_senha" class="text-sm font-medium text-slate-700">Confirmar nova senha</label>
                        <div class="mt-1 relative items-center gap-2">
                            <input type="password" id="confirmar_senha" name="confirmar_senha" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100" placeholder="Repita a nova senha" minlength="8" required>
                            <button type="button" class="toggle-password absolute inset-y-0 right-0 flex items-center pr-3   text-slate-700 h" data-target="confirmar_senha" title="Ver senha">
                                <i class="fa-solid fa-eye-slash"></i>
                            </button>
                        </div>
                        <?php $confirmarSenhaErr = old_error('confirmar_senha'); ?>
                        <?php if ($confirmarSenhaErr !== '') { ?>
                            <p class="mt-2 text-sm font-medium text-rose-700" id="confirmar-senha-error"><?php echo htmlspecialchars($confirmarSenhaErr); ?></p>
                        <?php } ?>

                    </div>

                    <div class="md:col-span-2 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="reset" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            <i class="fa-solid fa-rotate-left"></i>
                            Limpar
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            <i class="fa-solid fa-key"></i>
                            Salvar nova senha
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script type="module" src="<?= Url::path('/js/config.js') ?>"></script>
