<?php

declare(strict_types=1);

namespace Src\App\Services\IServices;

interface IEmailService
{
    public function enviarCodigoRecuperacao(string $email): void;

    public function solicitarCodigoRecuperacao(string $email): void;

    public function enviarMensagem(string $destinatario, string $assunto, string $corpo): void;

    public function verificarCodigo(string $email, string $codigo): bool;

    public function limparCodigo(): void;
}
