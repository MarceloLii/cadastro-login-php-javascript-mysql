<?php

namespace Models;

use \Engine\Database;
use \PDO;

/**
 * Classe UserModel
 * Gerencia os usuários do sistema, incluindo cadastro, autenticação e atualização de dados.
 */
class UserModel
{
    /** @var int Identificador único do usuário */
    public $id;

    /** @var string Nome completo do usuário */
    public $name;

    /** @var string Endereço de e-mail do usuário */
    public $email;

    /** @var string Hash da senha do usuário */
    public $password_hash;

    /** @var string Data e hora de criação do usuário */
    public $created_at;

    /** @var string Data e hora do último login */
    public $last_login;

    /** @var string Tipo de usuário (admin, editor, user) */
    public $user_type;

    /** @var string Status do usuário (active, inactive, banned) */
    public $status;

    /** @var Database Instância da classe Database */
    private $db;

    /**
     * Construtor da classe
     */
    public function __construct()
    {
        $this->db = new Database('users');
    }

    /**
     * Método para cadastrar um novo usuário no banco de dados
     * @return int|false Retorna o ID do usuário cadastrado ou false em caso de falha
     */
    public function cadastrar()
    {
        $this->created_at = date('Y-m-d H:i:s');

        return $this->db->insert([
            'name' => $this->name,
            'email' => $this->email,
            'password_hash' => $this->password_hash,
            'created_at' => $this->created_at,
            'user_type' => $this->user_type ?? 'user',
            'status' => $this->status ?? 'active',
            'failed_attempts' => 0
        ]);
    }

    /**
     * Método para atualizar os dados do usuário
     * @return bool Retorna true se a atualização for bem-sucedida
     */
    public function atualizar()
    {
        return $this->db->update('id = ?', [$this->id], [
            'name' => $this->name,
            'email' => $this->email,
            'password_hash' => $this->password_hash,
            'last_login' => $this->last_login,
            'user_type' => $this->user_type,
            'status' => $this->status
        ]);
    }

    /**
     * Método para excluir um usuário do banco de dados
     * @return bool Retorna true se a exclusão for bem-sucedida
     */
    public function excluir()
    {
        return $this->db->delete('id = ?', [$this->id]);
    }

    /**
     * Método para obter múltiplos usuários do banco de dados
     * @param string|null $where Condição para filtrar os usuários
     * @param array $params Parâmetros da condição
     * @param string|null $order Ordem dos resultados
     * @param string|null $limit Limite de resultados
     * @return array Lista de usuários
     */
    public static function getUsuarios($where = null, $params = [], $order = null, $limit = null)
    {
        $db = new Database('users');
        return $db->select($where, $params, $order, $limit);
    }

    /**
     * Método para obter um usuário pelo ID
     * @param int $id
     * @return UserModel|null Retorna o usuário como objeto UserModel ou null
     */
    public static function getUsuario($id)
    {
        $db = new Database('users');
        $resultado = $db->selectOne('id = ?', [$id]);

        if (!$resultado) {
            return null;
        }

        // Transforma stdClass em instância de UserModel
        $usuario = new self();
        $usuario->id = $resultado->id;
        $usuario->name = $resultado->name;
        $usuario->email = $resultado->email;
        $usuario->password_hash = $resultado->password_hash;
        $usuario->last_login = $resultado->last_login;
        $usuario->user_type = $resultado->user_type;
        $usuario->status = $resultado->status;

        return $usuario;
    }

    /**
     * Método para procurar um usuário pelo e-mail
     * @param string $email
     * @return object|null Retorna o usuário ou null se não encontrado
     */
    public static function getUsuarioPorEmail($email)
    {
        $db = new Database('users');
        return $db->selectOne('email = ?', [$email]);
    }

    /**
     * Método para autenticar um usuário pelo e-mail e senha
     * @param string $email
     * @param string $password
     * @return object|null Retorna o usuário autenticado ou null se falha
     */
    public static function autenticar($email, $password)
    {
        $usuario = self::getUsuarioPorEmail($email);
        if ($usuario && password_verify($password, $usuario->password_hash)) {
            return $usuario;
        }
        return null;
    }

    /**
     * Método para atualizar a data do último login e resetar falhas de login
     * @param int $id
     * @return bool Retorna true se a atualização for bem-sucedida
     */
    public static function atualizarLogin($id)
    {
        $db = new Database('users');
        return $db->update('id = ?', [$id], [
            'last_login' => date('Y-m-d H:i:s')
        ]);
    }
}
