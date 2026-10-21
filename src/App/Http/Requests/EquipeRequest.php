<?php

declare(strict_types=1);

namespace Src\App\Http\Requests;

use Src\Core\Request;

class EquipeRequest extends Request
{
    public function rules(): array
    {
        $fotoRule = empty($this->params['id'])
            ? ['required', 'fileType:jpg,png,jpeg,webp', 'fileSize:2048']
            : ['nullable', 'fileType:jpg,png,jpeg,webp', 'fileSize:2048'];

        return [
            'id'        => ['nullable', 'string'],
            'foto'      => $fotoRule,
            'nome'      => ['required', 'string', 'min:3', 'max:150'],
            'cargo'     => ['required', 'string', 'min:2', 'max:100'],
            'version'   => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'foto.required' => 'A foto é obrigatória.',
            'foto.fileType' => 'O tipo de arquivo da foto não é permitido.',
            'foto.fileSize' => 'O arquivo deve conter no máximo 2MB.',
            'nome.required' => 'O nome é obrigatório.',
            'nome.string'   => 'O nome deve ser um texto.',
            'nome.min'      => 'O nome deve ter pelo menos 3 caracteres.',
            'nome.max'      => 'O nome não pode exceder 150 caracteres.',
            'cargo.required'=> 'O cargo é obrigatório.',
            'cargo.string'  => 'O cargo deve ser um texto.',
        ];
    }
}
