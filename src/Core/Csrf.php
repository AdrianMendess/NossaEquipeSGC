<?php

declare(strict_types=1);

namespace Src\Core;

use Src\App\Utils\Toast;
use Src\App\Utils\Url;

class Csrf
{
    public static function create(): void
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
    }

    public static function check(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return;
        }

        $token = $_POST['csrf'] ?? '';

        if (! isset($_SESSION['csrf']) || ! hash_equals($_SESSION['csrf'], $token)) {
            unset($_SESSION['csrf']);

            Toast::error('Erro no envio do formulário. Por favor, tente novamente.');
            Url::redirect('/');
        }

    }
}
