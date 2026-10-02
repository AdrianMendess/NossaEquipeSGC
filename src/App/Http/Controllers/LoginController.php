<?php

declare(strict_types=1);

namespace Src\App\Http\Controllers;

use Exception;
use Src\App\Http\Exceptions\Usuario\UsuarioException;
use Src\App\Http\Requests\LoginRequest;
use Src\App\Http\Requests\RedefinirSenhaFormRequest;
use Src\App\Http\Requests\VerificaCodigoFormRequest;
use Src\App\Http\Requests\VerificarEmailFormRequest;
use Src\App\Services\IServices\IAuthService;
use Src\App\Services\IServices\IRecuperacaoSenhaService;
use Src\App\Services\IServices\IUserService;
use Src\App\Utils\Toast;
use Src\App\Utils\Url;
use Src\App\Utils\View;
use Src\Core\Auth;
use Src\Core\RateLimiting;

class LoginController extends SharedController
{
    private IAuthService $authService;
    private IUserService $userService;
    private IRecuperacaoSenhaService $recuperacaoSenhaService;

    public function __construct(
        IAuthService $authService,
        IUserService $userService,
        IRecuperacaoSenhaService $recuperacaoSenhaService
    ) {
        $this->authService = $authService;
        $this->userService = $userService;
        $this->recuperacaoSenhaService = $recuperacaoSenhaService;
    }

    public function index(): string
    {
        $erro = flash('erro_login') ?? '';
        $esqueciSenhaUrl = Url::path('/esqueci-senha');

        $content = View::render('Login/index', [
            'erro' => $erro,
            'formAction' => Url::path('/'),
            'esqueciSenha' => $esqueciSenhaUrl,
        ]);

        return self::getPage('PROJETO - LOGIN', $content, options: [
            'showSidebar' => false,
            'bodyClass' => 'login-page',
        ]);
    }

    public function autenticar(): void
    {
        $validated = (new LoginRequest($_POST))->redirectOnFail();

        $data = $validated->validated();

        try {
            $user = $this->authService->auth($data['email'], $data['senha']);

            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }

            Auth::attempt($user);
            RateLimiting::limpar(RateLimiting::getIp($data['email']));

            if (! $this->authService->checkAdmin()) {
                Url::redirect('/home');
            }

            Url::redirect('/admin');
        } catch (UsuarioException $e) {
            $_SESSION['erro_login'] = $e->getMessage();
            Url::redirect('/');
        } catch (Exception $e) {
            Toast::error('Ocorreu um erro inesperado. Tente novamente mais tarde.');
            Url::redirect('/');
        }
    }

    public function logout(): void
    {
        Auth::logout();
    }

    public function esqueciSenha(): string
    {
        $toast = Toast::pull();

        $content = View::render('Login/esqueci-senha', [
            'urlLogin' => Url::path('/'),
            'formActionVerificar' => Url::path('/esqueci-senha/verificar'),
            'toast' => $toast,
        ]);

        return parent::getPage('PROJETO - ESQUECI MINHA SENHA', $content, [
            'showSidebar' => false,
            'bodyClass' => 'login-page',
        ]);
    }

    public function verificarEmail(): string
    {
        $validated = (new VerificarEmailFormRequest($_POST))->redirectOnFail();
        $data = $validated->validated();
        $email = $data['email'];

        try {
            $this->recuperacaoSenhaService->solicitarCodigoRecuperacao($email);
        } catch (UsuarioException) {
            Toast::error('Email não encontrado.');
            Url::redirect('/esqueci-senha');
        } catch (Exception) {
            Toast::error('Erro ao enviar o código. Tente novamente.');
            Url::redirect('/esqueci-senha');
        }

        $toast = Toast::pull();

        $content = View::render('Login/esqueci-senha', [
            'codigoEnviado' => true,
            'emailParaCodigo' => $email,
            'timerExpira' => Auth::getExpiraCodigo(),
            'urlLogin' => Url::path('/'),
            'urlEsqueciSenha' => Url::path('/esqueci-senha'),
            'formActionVerificarCodigo' => Url::path('/esqueci-senha/verificar-codigo'),
        ]);

        return parent::getPage('PROJETO - VERIFICAR CÓDIGO', $content, [
            'showSidebar' => false,
            'bodyClass' => 'login-page',
            'toast' => $toast,
        ]);
    }

    public function verificarCodigo(): string
    {
        $validated = (new VerificaCodigoFormRequest($_POST))->redirectOnFail(Url::path('/esqueci-senha'));

        $data = $validated->validated();

        $email = $data['email'];
        $codigo = $data['codigo'];

        if (! $this->recuperacaoSenhaService->verificarCodigo($email, $codigo)) {
            if (Auth::codigoExpirado()) {
                Toast::error('Sessão expirada. Solicite um novo código.');
                Url::redirect('/esqueci-senha');
            }

            Toast::error('Código inválido. Tente novamente.');

            $content = View::render('Login/esqueci-senha', [
                'codigoEnviado' => true,
                'emailParaCodigo' => $email,
                'timerExpira' => Auth::getExpiraCodigo(),
                'urlLogin' => Url::path('/'),
                'urlEsqueciSenha' => Url::path('/esqueci-senha'),
                'formActionVerificarCodigo' => Url::path('/esqueci-senha/verificar-codigo'),
            ]);

            return parent::getPage('PROJETO - VERIFICAR CÓDIGO', $content, [
                'showSidebar' => false,
                'bodyClass' => 'login-page',
                'toast' => Toast::pull(),
            ]);
        }

        $this->recuperacaoSenhaService->limparCodigo();
        Auth::autorizarRedefinicao($email);

        $toast = Toast::pull();

        $content = View::render('Login/esqueci-senha', [
            'emailVerificado' => $email,
            'urlLogin' => Url::path('/'),
            'formActionNovaSenha' => Url::path('/esqueci-senha/redefinir'),
        ]);

        return self::getPage('PROJETO - REDEFINIR SENHA', $content, [
            'showSidebar' => false,
            'bodyClass' => 'login-page',
            'toast' => $toast,
        ]);
    }

    public function redefinirSenha()
    {
        if (! Auth::autorizadoMudarSenha()) {
            Toast::error('Ação não autorizada. Solicite um novo código.');
            Url::redirect('/esqueci-senha');
        }

        $emailAutorizado = Auth::getEmailAutorizado();

        $validated = new RedefinirSenhaFormRequest($_POST);

        if ($validated->fails()) {
            $content = View::render('Login/esqueci-senha', [
                'emailVerificado' => $emailAutorizado,
                'formActionNovaSenha' => Url::path('/esqueci-senha/redefinir'),
                'urlLogin' => Url::path('/'),
            ]);

            return parent::getPage('PROJETO - REDEFINIR SENHA', $content, [
                'showSidebar' => false,
                'bodyClass' => 'login-page',
                'toast' => Toast::pull(),
            ]);
        }

        $data = $validated->validated();
        $novaSenha = $data['nova_senha'];

        try {
            $this->userService->redefinirSenha($emailAutorizado, $novaSenha);

            Auth::limparAutorizacaoRedefinicao();

            Toast::success('Senha redefinida com sucesso!');
            Url::redirect('/');
        } catch (UsuarioException $e) {
            $content = View::render('Login/esqueci-senha', [
                'emailVerificado' => $emailAutorizado,
                'formActionNovaSenha' => Url::path('/esqueci-senha/redefinir'),
                'urlLogin' => Url::path('/'),
            ]);
            return parent::getPage('PROJETO - REDEFINIR SENHA', $content, [
                'showSidebar' => false,
                'bodyClass' => 'login-page',
                'toast' => Toast::pull(),
            ]);
        }
    }
}
