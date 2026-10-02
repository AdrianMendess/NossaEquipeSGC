<?php

declare(strict_types=1);

namespace Src\App\Utils;

class RecuperacaoSenhaEmailTemplate
{
    public static function render(string $codigo, string $expiraEmMinutos): string
    {
        $d1 = $codigo[0];
        $d2 = $codigo[1];
        $d3 = $codigo[2];
        $d4 = $codigo[3];
        $d5 = $codigo[4];
        $d6 = $codigo[5];

        return '
            <div style="font-family: Georgia, serif; max-width: 520px; margin: 0 auto; background-color: #efede0;
            border-radius: 12px; overflow: hidden; border: 1px solid rgba(21,11,2,0.15);">

                <div style="background: linear-gradient(135deg, #87181D, #5B0E06); padding: 30px 20px; text-align:
            center;">
                    <h1 style="color: #fbf8f4; font-size: 22px; margin: 0; font-weight: 600; font-family: Georgia, serif;
            letter-spacing: 0.5px;">Recupera&ccedil;&atilde;o de Senha</h1>
                </div>

                <div style="padding: 30px 25px; background-color: #fbf8f4;">

                    <p style="font-size: 15px; margin: 0 0 20px 0; color: #150b02; text-align: center;">
                        Voc&ecirc; solicitou a redefini&ccedil;&atilde;o da sua senha. Use o c&oacute;digo abaixo para
            continuar:
                    </p>

                    <div style="text-align: center; margin: 25px 0;">
                        <div style="display: inline-block; background-color: #efede0; border: 2px solid
            rgba(135,24,29,0.4); border-radius: 10px; padding: 18px 24px;">
                            <span style="font-size: 28px; font-weight: 700; letter-spacing: 8px; color: #150b02;
            font-family: monospace;">' . $d1 . $d2 . $d3 . ' <span style="color: #979287; font-weight: 300;">-</span> ' .
                        $d4 . $d5 . $d6 . '</span>
                        </div>
                    </div>

                    <p style="font-size: 13px; color: #979287; text-align: center; margin: 20px 0 5px 0;">
                        <strong style="color: #87181D;">&#9888;</strong> Este c&oacute;digo expira em <strong
            style="color: #150b02;">' . $expiraEmMinutos . ' minutos</strong>.
                    </p>

                    <hr style="border: none; border-top: 1px solid rgba(21,11,2,0.1); margin: 25px 0;">

                    <p style="font-size: 12px; color: #979287; text-align: center; margin: 0 0 5px 0;">
                        Se voc&ecirc; n&atilde;o solicitou esta recupera&ccedil;&atilde;o, ignore este email.
                    </p>
                    <p style="font-size: 12px; color: #979287; text-align: center; margin: 0;">
                        Esta mensagem &eacute; autom&aacute;tica, n&atilde;o &eacute; necess&aacute;rio responder.
                    </p>
                </div>

                <div style="background-color: #87181D; padding: 15px 20px; text-align: center;">
                    <p style="font-size: 11px; color: #fbf8f4; margin: 0; letter-spacing: 1px;">
                       <strong>PROJETO</strong>
                    </p>
                </div>
            </div>';
    }
}
