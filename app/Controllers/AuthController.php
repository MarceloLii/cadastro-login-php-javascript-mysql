<?php

namespace Controllers;

use Models\AuthModel;

class AuthController
{
    private $AuthModel;

    public function __construct()
    {
        $this->AuthModel = new AuthModel();
    }

    /**
     * function para logar no site
     */
    public function login()
    {
        // Processar o formulário de login
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                header('Location: /login?erro=tok');
                exit();
            }

            if (!empty($_POST['nome'])) {
                $username = $_POST['nome'];
            } else {
                header('Location: /login?erro=nome');
                exit();
            }
            if (!empty($_POST['senha'])) {
                $password = $_POST['senha'];
            } else {
                header('Location: /login?erro=senha');
                exit();
            }

            $user = $this->AuthModel->getUserByUsername($username);

            if ($user && password_verify($password, $user->senha)) {
                // Iniciar a sessão e redirecionar para o dashboard
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_type'] = $user->type;
                unset($_SESSION['csrf_token']);
                header('Location: /');
                exit();
            } else {
                header('Location: /login?erro=search');
                exit();
            }
        }

        // Gerar e armazenar o token CSRF
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        $title = 'Login';
        $robots = 'noindex,nofollow';
        include 'App/Views/Auth/login.php';
    }

    /**
     * Verifica se o usuário está logado e é um administrador
     * @return bool
     */
    public function isUsuarioAdmin()
    {
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
            return true;
        }

        return false;
    }

    public function dashboard()
    {
        // Verificar se o usuário está logado
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        // Carregar a view do dashboard
        include 'caminho/para/views/dashboard.php';
    }

    public function logout()
    {
        // Encerrar a sessão e redirecionar para a página de login
        session_start();
        session_destroy();
        header('Location: /login');
        exit();
    }
}
