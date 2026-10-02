<?php

declare(strict_types=1);

namespace Src\App\Infrastructure;

use RuntimeException;
use Src\Core\Logger;

class HttpEmailSender implements IEmailSender
{
    private const URL = 'https://api.email.ati.ma.gov.br/api/mensagens/enviar';
    private const TIMEOUT_SEGUNDOS = 5;

    public function send(string $destinatario, string $assunto, string $corpoHtml): void
    {
        $payload = json_encode([
            'destinatarios' => [$destinatario],
            'assunto' => $assunto,
            'corpo' => $corpoHtml,
        ]);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT_SEGUNDOS);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type:application/json',
            'Authorization: Bearer ' . $_ENV['CHAVE_EMAIL'],
        ]);
        curl_setopt($ch, CURLOPT_URL, self::URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $retorno = curl_exec($ch);
        $erro = $retorno === false ? curl_error($ch) : null;
        curl_close($ch);

        if ($retorno === false) {
            Logger::error('Falha na conexão com o serviço de email: ' . $erro);
            throw new RuntimeException('Falha ao conectar com o serviço de email.');
        }
    }
}
