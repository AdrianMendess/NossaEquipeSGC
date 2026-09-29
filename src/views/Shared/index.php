<!doctype html>
<html lang="pt-Br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="{{basePath}}/">
    <link rel="icon" href="images/favicon.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&display=swap">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="css/app.css">
    <title>{{title}}</title>
</head>

<?php
$bodyClass = $bodyClass ?? '';
$isLoginPage = is_string($bodyClass) && str_contains($bodyClass, 'login-page');
?>

<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased {{bodyClass}}">
    {{toast}}
    <?php if ($isLoginPage) : ?>
        <?php $classMain = $isLoginPage ? '' : 'min-h-screen px-4 py-8 sm:px-6'; ?>

        <main class="<?= $classMain ?>">
            {{content}}
        </main>
    <?php else : ?>
        <div class="flex h-screen overflow-hidden">
            <div id="sidebarBackdrop" class="fixed inset-0 z-40 hidden bg-slate-900/50 lg:hidden"></div>
            {{sidebar}}

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-30">
                    {{header}}
                </header>

                <main class="min-w-0 flex-1 overflow-y-auto p-4 sm:p-6">
                    {{content}}
                </main>

                <footer>
                    {{footer}}
                </footer>
            </div>
        </div>
    <?php endif; ?>

    <div id="modalExcluir" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4" aria-hidden="true">
        <div class="modal w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-0 shadow-xl">
            <div class="flex items-start gap-3 border-b border-slate-200 p-5">
                <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-base font-semibold text-slate-900">Confirmar exclusão</h3>
                    <p class="mt-1 text-sm text-slate-600">Esta ação não pode ser desfeita.</p>
                </div>
            </div>
            <div class="space-y-2 p-5 text-sm text-slate-700">
                <p>Tem certeza que deseja excluir <strong id="modalItemNome" class="font-semibold text-slate-900"></strong>?</p>
            </div>
            <div class="flex flex-col-reverse gap-2 border-t border-slate-200 p-5 sm:flex-row sm:justify-end">
                <button type="button" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50" id="btnCancelarExcluir">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </button>
                <form method="POST" id="formModalExcluir" class="w-full sm:w-auto" data-no-client-validation="true">
                    <?= csrf() ?>
                    <input type="hidden" id="modalItemId" name="id">
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 sm:w-auto">
                        <i class="fa-solid fa-trash"></i> Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script src="js/modal-excluir.js"></script>
    <?php if (! $isLoginPage) : ?>
        <script>
            (function () {
                var sidebar = document.getElementById('appSidebar');
                var backdrop = document.getElementById('sidebarBackdrop');
                var openBtn = document.getElementById('openSidebar');
                var closeBtn = document.getElementById('closeSidebar');
                if (!sidebar || !backdrop) return;

                var open = function () {
                    sidebar.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                };
                var close = function () {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                };

                if (openBtn) openBtn.addEventListener('click', open);
                if (closeBtn) closeBtn.addEventListener('click', close);
                backdrop.addEventListener('click', close);
                window.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') close();
                });
            })();
        </script>
    <?php endif; ?>

    <script type="module">
        import { showFieldError, hideFieldError } from './js/utils/util.js';

        function onlyDigits(value) {
            return String(value ?? '').replace(/\D+/g, '');
        }

        function maskValue(el) {
            var mask = el?.dataset?.mask;
            if (!mask) return;

            if (mask === 'number') {
                el.value = onlyDigits(el.value);
                return;
            }

            if (mask === 'year4') {
                el.value = onlyDigits(el.value).slice(0, 4);
                return;
            }
        }

        function getFieldMessage(el) {
            var labelEl = el?.closest('div')?.querySelector('label');
            var fieldName = labelEl?.textContent?.trim() || 'Este campo';

            if (el.validity.valueMissing) return fieldName + ' é obrigatório';
            if (el.validity.typeMismatch && el.type === 'email') return 'Informe um email válido';
            if (el.validity.tooShort) return 'Informe no mínimo ' + el.minLength + ' caracteres';
            if (el.validity.tooLong) return 'Informe no máximo ' + el.maxLength + ' caracteres';
            if (el.validity.patternMismatch) return el.dataset.patternMessage || 'Formato inválido';
            return 'Valor inválido';
        }

        function validateFileSize(el) {
            var maxKb = parseInt(el?.dataset?.maxSizeKb || '0', 10);
            if (!maxKb || !el.files || !el.files[0]) return true;

            var kb = el.files[0].size / 1024;
            if (kb > maxKb) {
                var mb = Math.round((maxKb / 1024) * 10) / 10;
                showFieldError(el, 'O arquivo não pode exceder ' + mb + ' MB');
                return false;
            }

            hideFieldError(el);
            return true;
        }

        function validateYearRange(form) {
            var anoInicio = form.querySelector('[name="ano_inicio"]');
            var anoFim = form.querySelector('[name="ano_fim"]');
            if (!anoInicio || !anoFim) return true;

            var inicio = parseInt(onlyDigits(anoInicio.value), 10);
            var fim = parseInt(onlyDigits(anoFim.value), 10);

            if (Number.isNaN(inicio) || Number.isNaN(fim)) return true;

            if (inicio > fim) {
                showFieldError(anoInicio, 'O ano de início não pode ser maior que o ano de término');
                showFieldError(anoFim, 'O ano de término deve ser maior ou igual ao ano de início');
                anoInicio.focus();
                return false;
            }

            hideFieldError(anoInicio);
            hideFieldError(anoFim);
            return true;
        }

        function validateForm(form, e) {
            if (form.hasAttribute('data-no-client-validation')) return;

            var elements = Array.from(form.querySelectorAll('input, select, textarea'))
                .filter(function (el) {
                    if (el.disabled) return false;
                    if (el.type === 'hidden') return false;
                    if (el.closest('[data-no-client-validation]')) return false;
                    return true;
                });

            for (var i = 0; i < elements.length; i++) {
                var el = elements[i];

                if (el.dataset && el.dataset.mask) maskValue(el);
                if (el.type === 'file' && !validateFileSize(el)) {
                    e.preventDefault();
                    el.focus();
                    return;
                }

                if (!el.checkValidity()) {
                    e.preventDefault();
                    showFieldError(el, getFieldMessage(el));
                    el.focus();
                    return;
                }

                hideFieldError(el);
            }

            if (!validateYearRange(form)) {
                e.preventDefault();
                return;
            }

            var submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            submitButtons.forEach(function (btn) {
                btn.disabled = true;
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-mask]').forEach(function (el) {
                el.addEventListener('input', function () {
                    maskValue(el);
                });
                maskValue(el);
            });

            document.querySelectorAll('input[type="file"][data-max-size-kb]').forEach(function (el) {
                el.addEventListener('change', function () {
                    validateFileSize(el);
                });
            });

            document.querySelectorAll('input, select, textarea').forEach(function (el) {
                el.addEventListener('input', function () {
                    hideFieldError(el);
                });
                el.addEventListener('change', function () {
                    hideFieldError(el);
                });
            });

            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    validateForm(form, e);
                });
            });
        });
    </script>
</body>

</html>
