<?php

declare(strict_types=1);

namespace Src\Core;

trait Validations
{
    public function string(string $key, mixed $value, mixed $arg = null): void
    {
        if (is_string($value)) {
            $this->addSuccess($key, trim(strip_tags($value)));
            return;
        }

        $this->addError($key, $value, ['string' => "O campo {$key} deve ser uma string."]);
    }

    public function integer(string $key, mixed $value, mixed $arg = null): void
    {
        if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
            $this->addSuccess($key, (int) $value);
            return;
        }

        $this->addError($key, $value, ['integer' => "O campo {$key} deve ser um número inteiro."]);
    }

    public function numeric(string $key, mixed $value, mixed $arg = null): void
    {
        if (is_numeric($value)) {
            $this->addSuccess($key, $value + 0);
            return;
        }

        $this->addError($key, $value, ['numeric' => "O campo {$key} deve ser numérico."]);
    }

    public function boolean(string $key, mixed $value, mixed $arg = null): void
    {
        if (in_array($value, [true, false, 1, 0, '1', '0', 'true', 'false'], true)) {
            $this->addSuccess($key, filter_var($value, FILTER_VALIDATE_BOOLEAN));
            return;
        }

        $this->addError($key, $value, ['boolean' => "O campo {$key} deve ser verdadeiro ou falso."]);
    }

    public function required(string $key, mixed $value, mixed $arg = null): void
    {
        if (is_string($value)) {
            $value = trim($value);
        }

        if ($value === null || $value === '') {
            $this->addError($key, $value, ['required' => "O campo {$key} é obrigatório."]);
            return;
        }

        $this->addSuccess($key, $value);
    }

    public function nullable(string $key, mixed $value, mixed $arg = null): void
    {
        if ($value === null || $value === '') {
            $this->addSuccess($key, $value);
            $this->skipField($key);
            return;
        }

        if (is_array($value) && isset($value['error']) && $value['error'] === UPLOAD_ERR_NO_FILE) {
            $this->addSuccess($key, null);
            $this->skipField($key);
        }
    }

    public function email(string $key, mixed $value, mixed $arg = null): void
    {
        $value = trim(strtolower((string) $value));

        if (! filter_var($value, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE)) {
            $this->addError($key, $value, ['email' => "O campo {$key} deve ser um e-mail válido."]);
            return;
        }

        $domain = substr(strrchr($value, '@'), 1);
        if (! checkdnsrr($domain, 'MX')) {
            $this->addError($key, $value, ['email' => "O domínio do e-mail informado em {$key} não existe."]);
            return;
        }

        $this->addSuccess($key, $value);
    }

    public function url(string $key, mixed $value, mixed $arg = null): void
    {
        $value = trim((string) $value);

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addSuccess($key, $value);
            return;
        }

        $this->addError($key, $value, ['url' => "O campo {$key} deve ser uma URL válida."]);
    }

    public function regex(string $key, mixed $value, mixed $arg): void
    {
        if ($arg === null) {
            throw new \InvalidArgumentException('Regra regex requer um padrão. Ex: regex:/^[a-z]+$/');
        }

        $value = is_string($value) ? trim($value) : $value;

        if (preg_match($arg, (string) $value)) {
            $this->addSuccess($key, $value);
            return;
        }

        $this->addError($key, $value, ['regex' => "O campo {$key} possui formato inválido."]);
    }

    public function in(string $key, mixed $value, mixed $arg): void
    {
        if ($arg === null) {
            throw new \InvalidArgumentException("Regra 'in' requer valores. Ex: in:admin,user");
        }

        $value = is_string($value) ? trim($value) : $value;
        $allowed = explode(',', $arg);

        if (in_array((string) $value, $allowed, true)) {
            $this->addSuccess($key, $value);
            return;
        }

        $this->addError($key, $value, ['in' => "O campo {$key} deve ser um dos valores: {$arg}."]);
    }

    public function min(string $key, mixed $value, mixed $limit): void
    {
        if ($limit === null) {
            throw new \InvalidArgumentException("Regra 'min' requer um limite. Ex: min:3");
        }

        if ($value === null) {
            return;
        }

        // 🔒 força comportamento consistente
        if (is_string($value) || is_numeric($value)) {
            $value = (string) $value;

            if (strlen($value) >= (int) $limit) {
                $this->addSuccess($key, $value);
                return;
            }

            $this->addError($key, $value, [
                'min' => "O campo {$key} deve ter no mínimo {$limit} caracteres.",
            ]);

            return;
        }

        if (is_array($value) && count($value) >= (int) $limit) {
            $this->addSuccess($key, $value);
            return;
        }

        $this->addError($key, $value, [
            'min' => "O campo {$key} deve ter no mínimo {$limit}.",
        ]);
    }

    public function max(string $key, mixed $value, mixed $limit): void
    {
        if ($limit === null) {
            throw new \InvalidArgumentException("Regra 'max' requer um limite. Ex: max:100");
        }

        if (is_string($value) && strlen($value) <= (int) $limit) {
            $this->addSuccess($key, $value);
            return;
        }

        if (is_numeric($value) && $value <= (float) $limit) {
            $this->addSuccess($key, $value);
            return;
        }

        if (is_array($value) && count($value) <= (int) $limit) {
            $this->addSuccess($key, $value);
            return;
        }

        $this->addError($key, $value, ['max' => "O campo {$key} deve ter no máximo {$limit}."]);
    }

    /**
     * Valida a extensão/tipo do arquivo.
     * $value deve ser o array de $_FILES['campo'].
     * Ex: fileType:jpg,jpeg,png,pdf
     */
    public function fileType(string $key, mixed $value, mixed $arg): void
    {
        if ($arg === null) {
            throw new \InvalidArgumentException("Regra 'fileType' requer extensões. Ex: fileType:jpg,png");
        }

        if (! is_array($value) || ! isset($value['name'], $value['error'])) {
            $this->addError($key, $value, ['fileType' => "O campo {$key} deve ser um arquivo."]);
            return;
        }

        if ($value['error'] !== UPLOAD_ERR_OK) {
            $this->addError($key, $value, ['fileType' => "Ocorreu um erro ao enviar o arquivo {$key}."]);
            return;
        }

        $allowed = array_map('strtolower', explode(',', $arg));
        $extension = strtolower(pathinfo($value['name'], PATHINFO_EXTENSION));

        if (in_array($extension, $allowed, true)) {
            $this->addSuccess($key, $value);
            return;
        }

        $this->addError($key, $value, ['fileType' => "O arquivo {$key} deve ter uma das extensões: {$arg}."]);
    }

    /**
     * Valida o tamanho máximo do arquivo em kilobytes.
     * Ex: fileSize:2048  → máximo 2 MB
     */
    public function fileSize(string $key, mixed $value, mixed $arg): void
    {
        if ($arg === null) {
            throw new \InvalidArgumentException("Regra 'fileSize' requer tamanho em KB. Ex: fileSize:2048");
        }

        if (! is_array($value) || ! isset($value['size'], $value['error'])) {
            $this->addError($key, $value, ['fileSize' => "O campo {$key} deve ser um arquivo."]);
            return;
        }

        if ($value['error'] !== UPLOAD_ERR_OK) {
            $this->addError($key, $value, ['fileSize' => "Ocorreu um erro ao enviar o arquivo {$key}."]);
            return;
        }

        $maxBytes = (int) $arg * 1024;

        if ($value['size'] <= $maxBytes) {
            $this->addSuccess($key, $value);
            return;
        }

        $this->addError($key, $value, ['fileSize' => "O arquivo {$key} deve ter no máximo {$arg} KB."]);
    }

    /**
     * Garante que o valor NÃO exista na tabela (ex: e-mail único).
     * Ex: unique:users,email
     */
    public function unique(string $key, mixed $value, mixed $arg): void
    {
        [$table, $column] = $this->parseDbArg('unique', $arg);

        $stmt = $this->pdo()->prepare("SELECT 1 FROM {$table} WHERE {$column} = :value LIMIT 1");
        $stmt->bindValue(':value', $value);
        $stmt->execute();

        if ($stmt->fetchColumn()) {
            $this->addError($key, $value, ['unique' => "O valor informado para {$key} já está em uso."]);
            return;
        }

        $this->addSuccess($key, $value);
    }

    /**
     * Garante que o valor EXISTA na tabela (ex: chave estrangeira).
     * Ex: exists:categories,id
     */
    public function exists(string $key, mixed $value, mixed $arg): void
    {
        [$table, $column] = $this->parseDbArg('exists', $arg);

        $stmt = $this->pdo()->prepare("SELECT 1 FROM {$table} WHERE {$column} = :value LIMIT 1");
        $stmt->bindValue(':value', $value);
        $stmt->execute();

        if ($stmt->fetchColumn()) {
            $this->addSuccess($key, $value);
            return;
        }

        $this->addError($key, $value, ['exists' => "O valor informado para {$key} não foi encontrado."]);
    }

    /**
     * Verifica se o valor é uma data válida.
     */
    public function date(string $key, mixed $value, mixed $arg = null): void
    {
        if (strtotime((string) $value) !== false) {
            $this->addSuccess($key, $value);
            return;
        }

        $this->addError($key, $value, ['date' => "O campo {$key} deve ser uma data válida."]);
    }

    /**
     * Verifica se a data está no formato especificado.
     * Ex: dateFormat:Y-m-d
     */
    public function dateFormat(string $key, mixed $value, mixed $arg): void
    {
        if ($arg === null) {
            throw new \InvalidArgumentException("Regra 'dateFormat' requer um formato. Ex: dateFormat:Y-m-d");
        }

        $parsed = \DateTime::createFromFormat($arg, (string) $value);

        if ($parsed && $parsed->format($arg) === (string) $value) {
            $this->addSuccess($key, $value);
            return;
        }

        $this->addError($key, $value, ['dateFormat' => "O campo {$key} deve estar no formato {$arg}."]);
    }

    /**
     * Verifica se a data é posterior à data informada.
     * Ex: dateAfter:2000-01-01  ou  dateAfter:today
     */
    public function dateAfter(string $key, mixed $value, mixed $arg): void
    {
        if ($arg === null) {
            throw new \InvalidArgumentException("Regra 'dateAfter' requer uma data de referência.");
        }

        $input = strtotime((string) $value);
        $reference = strtotime($arg === 'today' ? date('Y-m-d') : $arg);

        if ($input !== false && $reference !== false && $input > $reference) {
            $this->addSuccess($key, $value);
            return;
        }

        $this->addError($key, $value, ['dateAfter' => "O campo {$key} deve ser uma data posterior a {$arg}."]);
    }

    /**
     * Verifica se a data é posterior ou igual à data informada.
     * Ex: dateAfterOrEqual:today
     */
    public function dateAfterOrEqual(string $key, mixed $value, mixed $arg): void
    {
        if ($arg === null) {
            throw new \InvalidArgumentException("Regra 'dateAfterOrEqual' requer uma data de referência.");
        }

        $input = strtotime((string) $value);
        $reference = strtotime($arg === 'today' ? date('Y-m-d') : $arg);

        if ($input !== false && $reference !== false && $input >= $reference) {
            $this->addSuccess($key, $value);
            return;
        }

        $this->addError($key, $value, ['dateAfterOrEqual' => "O campo {$key} não pode ser anterior a {$arg}."]);
    }

    /**
     * Verifica se a data é anterior à data informada.
     * Ex: dateBefore:today  ou  dateBefore:2030-12-31
     */
    public function dateBefore(string $key, mixed $value, mixed $arg): void
    {
        if ($arg === null) {
            throw new \InvalidArgumentException("Regra 'dateBefore' requer uma data de referência.");
        }

        $input = strtotime((string) $value);
        $reference = strtotime($arg === 'today' ? date('Y-m-d') : $arg);

        if ($input !== false && $reference !== false && $input < $reference) {
            $this->addSuccess($key, $value);
            return;
        }

        $this->addError($key, $value, ['dateBefore' => "O campo {$key} deve ser uma data anterior a {$arg}."]);
    }

    /**
     * Garante que o valor seja igual ao de outro campo do mesmo envio.
     * Ex: same:nova_senha
     */
    public function same(string $key, mixed $value, mixed $arg): void
    {
        if ($arg === null) {
            throw new \InvalidArgumentException("Regra 'same' requer um campo de referência. Ex: same:nova_senha");
        }

        $outroValor = $this->params[$arg] ?? null;

        if ($value === $outroValor) {
            $this->addSuccess($key, $value);
            return;
        }

        $this->addError($key, $value, ['same' => "O campo {$key} deve ser igual a {$arg}."]);
    }

    private function parseDbArg(string $rule, mixed $arg): array
    {
        if (empty($arg) || ! str_contains($arg, ',')) {
            throw new \InvalidArgumentException("Regra '{$rule}' inválida. Use: {$rule}:tabela,coluna");
        }

        [$table, $column] = explode(',', $arg, 2);

        if (! preg_match('/^[a-zA-Z0-9_]+$/', $table) || ! preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
            throw new \InvalidArgumentException("Nome de tabela ou coluna inválido em '{$rule}'.");
        }

        return [$table, $column];
    }
}
