<?php

declare(strict_types=1);

namespace Src\App\Http\Requests;

use Src\Core\Request;

// TODO: adapte as regras e mensagens para o seu domínio
class ClienteRequest extends Request
{
    public function rules(): array
    {
        return [
            'id'        => ['nullable', 'string'],
            'nome'    => ['required', 'string', 'min:3', 'max:150'],
            'tipo' => ['nullable', 'string', 'max:500'],
            'version'   => ['nullable', 'integer'],
            'status'    => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O título é obrigatório.',
            'nome.string'   => 'O título deve ser um texto.',
            'nome.min'      => 'O título deve ter pelo menos 3 caracteres.',
            'nome.max'      => 'O título não pode exceder 150 caracteres.',
            'tipo.max'   => 'A descrição não pode exceder 500 caracteres.',
        ];
    }
}
