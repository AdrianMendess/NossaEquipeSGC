<?php

declare(strict_types=1);

namespace Src\App\Http\Requests;

use Src\Core\Request;

class AtualizarSenhaFormRequest extends Request
{
    public function rules(): array
    {
        return [
            'senha_atual' => ['required', 'string', 'min:8', 'max:150'],
            'nova_senha' => ['required', 'string', 'min:8', 'max:150'],
            'confirmar_senha' => ['required', 'string', 'min:8', 'max:150', 'same:nova_senha'],
        ];
    }

    public function messages(): array
    {
        return [
            'senha_atual.required' => 'O campo senha atual é obrigatório.',
            'senha_atual.string' => 'O campo senha atual deve ser uma string.',
            'senha_atual.min' => 'O campo senha atual deve ter no mínimo 8 caracteres.',
            'senha_atual.max' => 'O campo senha atual deve ter no máximo 150 caracteres.',

            'nova_senha.required' => 'O campo nova senha é obrigatório.',
            'nova_senha.string' => 'O campo nova senha deve ser uma string.',
            'nova_senha.min' => 'O campo nova senha deve ter no mínimo 8 caracteres.',
            'nova_senha.max' => 'O campo nova senha deve ter no máximo 150 caracteres.',

            'confirmar_senha.required' => 'O campo confirmar senha é obrigatório.',
            'confirmar_senha.string' => 'O campo confirmar senha deve ser uma string.',
            'confirmar_senha.min' => 'O campo confirmar senha deve ter no mínimo 8 caracteres.',
            'confirmar_senha.max' => 'O campo confirmar senha deve ter no máximo 150 caracteres.',
            'confirmar_senha.same' => 'A confirmação não coincide com a nova senha.',
        ];
    }
}
