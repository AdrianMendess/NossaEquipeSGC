<?php

declare(strict_types=1);

namespace Src\App\Http\Exceptions\Usuario;

use Src\App\Enums\HttpStatus;
use Src\App\Http\Exceptions\DomainException;

class UsuarioException extends DomainException
{
    public function __construct(string $mensagem = '', int $statusCode = HttpStatus::BAD_REQUEST->value)
    {
        parent::__construct($mensagem);
        $this->statusCode = $statusCode;
    }

    public static function naoEncontrado(string $mensagem = 'Usuário não encontrado.'): self
    {
        return new self($mensagem, HttpStatus::NOT_FOUND->value);
    }

    public static function credenciaisInvalidas(): self
    {
        return new self('Usuário ou senha incorretos.', HttpStatus::UNAUTHORIZED->value);
    }

    public static function senhaInvalida(array|string $motivo): self
    {
        $mensagem = is_array($motivo) ? implode(', ', $motivo) : $motivo;
        return new self('Senha inválida: ' . $mensagem, HttpStatus::UNPROCESSABLE_ENTITY->value);
    }

    public static function emailInvalido($mensagem = 'Email inválido.'): self
    {
        return new self($mensagem, HttpStatus::CONFLICT->value);
    }

    public static function statusInvalido($mensagem = 'Status inválido.'): self
    {
        return new self($mensagem, HttpStatus::UNPROCESSABLE_ENTITY->value);
    }

    public static function naoPodeDesativarProprioUsuario(): self
    {
        return new self('Você não pode desativar a sua própria conta.', HttpStatus::UNPROCESSABLE_ENTITY->value);
    }

    public static function perfilInvalido($mensagem = 'Perfil inválido.'): self
    {
        return new self($mensagem, HttpStatus::UNPROCESSABLE_ENTITY->value);
    }

    public static function conflitoDeEdicao(
        string $mensagem = 'Este usuário foi alterado por outra pessoa. Recarregue a página e tente novamente.'
    ): self {
        return new self($mensagem, HttpStatus::CONFLICT->value);
    }
}
