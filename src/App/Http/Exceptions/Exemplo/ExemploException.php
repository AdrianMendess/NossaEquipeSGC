<?php

declare(strict_types=1);

namespace Src\App\Http\Exceptions\Exemplo;

use Src\App\Http\Exceptions\DomainException;

class ExemploException extends DomainException
{
    public static function erroCriar(): self
    {
        return new self('Erro ao criar registro.');
    }

    public static function erroAtualizar(): self
    {
        return new self('Erro ao atualizar registro.');
    }

    public static function naoEncontrado(): self
    {
        return new self('Registro não encontrado.');
    }

    public static function erroDeletar(): self
    {
        return new self('Erro ao deletar registro.');
    }

    public static function conflitoDeEdicao(): self
    {
        return new self('Este registro foi alterado por outra pessoa. Recarregue a página e tente novamente.');
    }
}
