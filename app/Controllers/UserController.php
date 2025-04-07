<?php
/**
 * UserController.php
 *
 * Controlador responsável por gerenciar usuários:
 * - Cadastro, login, atualização, exclusão, listagem e exibição de detalhes.
 *
 * Segurança:
 * - Proteção contra CSRF.
 * - Validação e sanitização de entradas.
 * - Controle de permissões (admin e usuário).
 * - Senhas criptografadas.
 *
 * @author Marcelo Lima
 * @github https://github.com/MarceloLima
 */

namespace Controllers;

use Models\UserModel;
use Controllers\Controller;

class UserController
{
    /**
     * Exibe o formulário e trata o cadastro de novo usuário.
     */
    public function cadastrar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Proteção contra CSRF
            if (empty($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                Controller::adicionarMensagem('erro', 'Token CSRF inválido.');
                header('Location: /cadastro');
                exit();
            }

            // Verificação de campos obrigatórios
            if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password'])) {
                Controller::adicionarMensagem('erro', 'Todos os campos são obrigatórios.');
                header('Location: /cadastro');
                exit();
            }

            // Sanitização
            $nome = Controller::escaparHtml($_POST['name']);
            $email = Controller::escaparHtml($_POST['email']);
            $senha = $_POST['password'];

            // Validações
            if (strlen($nome) < 3) {
                Controller::adicionarMensagem('erro', 'O nome deve ter no mínimo 3 caracteres.');
                header('Location: /cadastro');
                exit();
            }

            if (!preg_match('/^(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{6,}$/', $senha)) {
                Controller::adicionarMensagem('erro', 'A senha deve ter no mínimo 6 caracteres e ao menos um caractere especial.');
                header('Location: /cadastro');
                exit();
            }

            if (UserModel::getUsuarioPorEmail($email)) {
                Controller::adicionarMensagem('erro', 'Este e-mail já está cadastrado.');
                header('Location: /cadastro');
                exit();
            }

            // Criptografia da senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            // Criação do usuário
            $usuario = new UserModel();
            $usuario->name = $nome;
            $usuario->email = $email;
            $usuario->password_hash = $senha_hash;

            if ($usuario->cadastrar()) {
                Controller::adicionarMensagem('sucesso', 'Usuário cadastrado com sucesso.');
                header('Location: /usuarios');
                exit();
            } else {
                Controller::adicionarMensagem('erro', 'Erro ao cadastrar usuário.');
                header('Location: /cadastro');
                exit();
            }
        }

        // View do formulário de cadastro
        $titulo_da_pagina = 'Cadastro de Usuário';
        $viewPath = 'app/Views/Users/cadastro.php';
        Controller::exibirMensagens();
        include 'app/template.php';
    }

    /**
     * Atualiza dados de um usuário.
     * Apenas usuários administradores podem acessar.
     *
     * @param int $id ID do usuário
     */
    public function atualizar($id)
    {
        if (!isset($_SESSION['usuario'])) {
            Controller::adicionarMensagem('erro', 'Você precisa estar logado.');
            header('Location: /login');
            exit();
        }

        $usuarioLogado = $_SESSION['usuario'];
        $usuario = UserModel::getUsuario($id);

        if (!$usuario || $usuarioLogado['user_type'] !== 'admin') {
            Controller::adicionarMensagem('erro', 'Você não tem permissão para atualizar este usuário.');
            header('Location: /');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (empty($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                Controller::adicionarMensagem('erro', 'Token CSRF inválido.');
                header("Location: /update/{$id}");
                exit();
            }

            $nome = Controller::escaparHtml($_POST['name'] ?? '');
            $email = Controller::escaparHtml($_POST['email'] ?? '');
            $senha = $_POST['password'] ?? '';
            $user_type = Controller::escaparHtml($_POST['user_type'] ?? $usuario->user_type);
            $status = Controller::escaparHtml($_POST['status'] ?? $usuario->status);

            if (empty($nome) || empty($email)) {
                Controller::adicionarMensagem('erro', 'Nome e e-mail são obrigatórios.');
                header("Location: /update/{$id}");
                exit();
            }

            if (strlen($nome) < 3) {
                Controller::adicionarMensagem('erro', 'O nome deve ter no mínimo 3 caracteres.');
                header("Location: /update/{$id}");
                exit();
            }

            $emailExistente = UserModel::getUsuarioPorEmail($email);
            if ($emailExistente && $emailExistente->id != $id) {
                Controller::adicionarMensagem('erro', 'Este e-mail já está em uso.');
                header("Location: /update/{$id}");
                exit();
            }

            if (!empty($senha)) {
                if (!preg_match('/^(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{6,}$/', $senha)) {
                    Controller::adicionarMensagem('erro', 'A senha deve ter no mínimo 6 caracteres e ao menos um caractere especial.');
                    header("Location: /update/{$id}");
                    exit();
                }
                $usuario->password_hash = password_hash($senha, PASSWORD_DEFAULT);
            }

            $usuario->name = $nome;
            $usuario->email = $email;
            $usuario->user_type = $user_type;
            $usuario->status = $status;

            if ($usuario->atualizar()) {
                Controller::adicionarMensagem('sucesso', 'Usuário atualizado com sucesso.');
                header('Location: /usuarios');
                exit();
            } else {
                Controller::adicionarMensagem('erro', 'Erro ao atualizar usuário.');
                header("Location: /update/{$id}");
                exit();
            }
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }        

        $titulo_da_pagina = 'Atualizar dados do Usuário';
        $viewPath = 'app/Views/Users/update.php';
        Controller::exibirMensagens();
        include 'app/template.php';
    }

    /**
     * Exclui um usuário.
     * Apenas o próprio dono ou um admin pode excluir.
     * Se for o próprio usuário, será deslogado após exclusão.
     *
     * @param int $id ID do usuário
     */
    public function excluir($id)
    {
        if (!isset($_SESSION['usuario'])) {
            Controller::adicionarMensagem('erro', 'Você precisa estar logado.');
            header('Location: /login');
            exit();
        }

        $usuario = UserModel::getUsuario($id);
        $logado = $_SESSION['usuario'];

        if (!$usuario) {
            Controller::adicionarMensagem('erro', 'Usuário não encontrado.');
            header('Location: /usuarios');
            exit();
        }

        // Apenas o próprio usuário ou admin pode excluir
        if ($logado['id'] !== $usuario->id && $logado['user_type'] !== 'admin') {
            Controller::adicionarMensagem('erro', 'Você não tem permissão para excluir este usuário.');
            header('Location: /usuarios');
            exit();
        }

        if ($usuario->excluir()) {
            // Se o usuário excluído for o logado, desloga
            if ($logado['id'] === $usuario->id) {
                $_SESSION = [];
                session_destroy();
                session_start();
                session_regenerate_id(true);
                Controller::adicionarMensagem('sucesso', 'Conta excluída com sucesso. Você foi deslogado.');
                header('Location: /login');
                exit();
            } else {
                Controller::adicionarMensagem('sucesso', 'Usuário excluído com sucesso.');
                header('Location: /usuarios');
                exit();
            }
        } else {
            Controller::adicionarMensagem('erro', 'Erro ao excluir usuário.');
            header('Location: /usuarios');
            exit();
        }
    }

    /**
     * Lista todos os usuários com opção de filtro.
     */
    public function listarUsuarios($filtro = null)
    {
        if (!isset($_SESSION['usuario'])) {
            Controller::adicionarMensagem('erro', 'Você precisa estar logado.');
            header('Location: /login');
            exit();
        }

        $usuarios = UserModel::getUsuarios($filtro);
        if (empty($usuarios)) {
            Controller::adicionarMensagem('erro', 'Nenhum usuário encontrado.');
        }

        $titulo_da_pagina = 'Lista de Usuários';
        $viewPath = 'app/Views/Users/listar_usuarios.php';
        Controller::exibirMensagens();
        include 'app/template.php';
    }

    /**
     * Exibe os dados de um usuário pelo ID.
     */
    public function obterUsuario($id)
    {
        if (!isset($_SESSION['usuario'])) {
            Controller::adicionarMensagem('erro', 'Você precisa estar logado.');
            header('Location: /login');
            exit();
        }

        $usuario = UserModel::getUsuario($id);

        if ($usuario) {
            Controller::adicionarMensagem('sucesso', 'Usuário encontrado com sucesso.');
        } else {
            Controller::adicionarMensagem('erro', 'Usuário não encontrado.');
        }

        $titulo_da_pagina = 'Detalhes do Usuário';
        $viewPath = 'app/Views/Auth/detalhes_usuario.php';
        Controller::exibirMensagens();
        include 'app/template.php';
    }

    /**
     * Login de usuários com verificação de credenciais.
     */
    public function login()
    {
        if (isset($_SESSION['usuario'])) {
            Controller::adicionarMensagem('sucesso', 'Você já está logado!');
            header('Location: /');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (empty($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                Controller::adicionarMensagem('erro', 'Token CSRF inválido.');
                header('Location: /login');
                exit();
            }

            $email = Controller::escaparHtml($_POST['email'] ?? '');
            $senha = $_POST['password'] ?? '';

            if (empty($email) || empty($senha)) {
                Controller::adicionarMensagem('erro', 'E-mail e senha são obrigatórios.');
                header('Location: /login');
                exit();
            }

            $usuario = UserModel::getUsuarioPorEmail($email);

            if ($usuario && password_verify($senha, $usuario->password_hash)) {
                UserModel::atualizarLogin($usuario->id);
                $_SESSION['usuario'] = [
                    'id' => $usuario->id,
                    'name' => $usuario->name,
                    'email' => $usuario->email,
                    'user_type' => $usuario->user_type
                ];
                Controller::adicionarMensagem('sucesso', 'Login realizado com sucesso.');
                header('Location: /');
                exit();
            } else {
                Controller::adicionarMensagem('erro', 'Credenciais inválidas.');
                header('Location: /login');
                exit();
            }
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }        

        $titulo_da_pagina = 'Login de Usuário';
        $viewPath = 'app/Views/Users/login.php';
        Controller::exibirMensagens();
        include 'app/template.php';
    }

    /**
     * Logout do sistema com limpeza de sessão.
     */
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();
        session_start();
        session_regenerate_id(true);
        Controller::adicionarMensagem('sucesso', 'Você saiu com sucesso.');
        header('Location: /login');
        exit();
    }
}
