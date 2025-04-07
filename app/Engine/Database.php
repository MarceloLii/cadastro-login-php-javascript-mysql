<?php

namespace Engine;

use \PDO;
use \PDOException;

/**
 * Classe Database
 * Responsável por gerenciar a conexão com o banco de dados e executar consultas SQL de forma segura.
 */
class Database
{
    /** @var string Nome da tabela a ser manipulada */
    private $table;

    /** @var PDO Instância da conexão com o banco de dados */
    private static $connection;

    /**
     * Construtor da classe
     * @param string|null $table Nome da tabela a ser utilizada
     */
    public function __construct($table = null)
    {
        $this->table = $table;
        $this->setConnection();
    }

    /**
     * Método responsável por carregar as variáveis do arquivo .env
     */
    private function loadEnv()
    {
        $envPath = __DIR__ . '/.env'; // Caminho do arquivo .env dentro da pasta Engine

        if (!file_exists($envPath)) {
            die('ERROR: Arquivo .env não encontrado!');
        }

        // Ler as linhas do arquivo .env
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Ignorar comentários
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Separar a chave e o valor
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }

    /**
     * Método responsável por estabelecer a conexão segura com o banco de dados.
     */
    private function setConnection()
    {
        // Evita criar múltiplas conexões desnecessárias
        if (!isset(self::$connection)) {
            $this->loadEnv(); // Carrega as variáveis do .env

            try {
                // Obter as variáveis do ambiente
                $dsn = "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME') . ";charset=utf8";
                self::$connection = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'));

                // Configurar o PDO para lançar exceções em caso de erro
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            } catch (PDOException $e) {
                die('ERROR: Falha na conexão com o banco de dados: ' . $e->getMessage());
            }
        }
    }

    /**
     * Método responsável por executar queries no banco de dados.
     * @param string $query SQL a ser executado
     * @param array $params Parâmetros a serem vinculados à query
     * @return PDOStatement|false Retorna a resposta da query ou false em caso de falha
     */
    public function execute($query, $params = [])
    {
        try {
            $statement = self::$connection->prepare($query);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $e) {
            die('ERROR: Erro ao executar a query: ' . $e->getMessage());
        }
    }

    /**
     * Método responsável por inserir dados na tabela.
     * @param array $values Dados a serem inseridos [coluna => valor]
     * @return int Retorna o ID do registro inserido
     */
    public function insert(array $values)
    {
        $fields = array_keys($values);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));

        // Montar a query SQL
        $query = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES ({$placeholders})";

        // Executar a inserção e retornar o ID inserido
        $this->execute($query, array_values($values));
        return self::$connection->lastInsertId();
    }

    /**
     * Método responsável por buscar registros na tabela.
     * @param string|null $where Condição da busca (ex: "id = ?")
     * @param array $params Parâmetros para a cláusula WHERE
     * @param string|null $order Ordem da busca (ex: "id DESC")
     * @param string|null $limit Limite de registros (ex: "10")
     * @param string $fields Campos a serem retornados (padrão: "*")
     * @return array Lista de registros encontrados
     */
    public function select($where = null, array $params = [], $order = null, $limit = null, $fields = '*')
    {
        $whereClause = $where ? "WHERE $where" : '';
        $orderClause = $order ? "ORDER BY $order" : '';
        $limitClause = $limit ? "LIMIT $limit" : '';

        $query = "SELECT $fields FROM {$this->table} $whereClause $orderClause $limitClause";

        // Executar a consulta e retornar os resultados como objetos
        return $this->execute($query, $params)->fetchAll();
    }

    /**
     * Método responsável por buscar um único registro na tabela.
     * @param string $where Condição da busca (ex: "id = ?")
     * @param array $params Parâmetros para a cláusula WHERE
     * @param string $fields Campos a serem retornados (padrão: "*")
     * @return object|null Retorna o objeto do registro ou null se não encontrado
     */
    public function selectOne($where, array $params = [], $fields = '*')
    {
        $query = "SELECT $fields FROM {$this->table} WHERE $where LIMIT 1";
        $stmt = $this->execute($query, $params);
        return $stmt ? $stmt->fetch() : null;
    }

    /**
     * Método responsável por atualizar registros na tabela.
     * @param string $where Condição para atualizar os registros (ex: "id = ?")
     * @param array $params Parâmetros para a cláusula WHERE
     * @param array $values Dados a serem atualizados [coluna => valor]
     * @return bool Retorna true se a atualização for bem-sucedida
     */
    public function update($where, array $params, array $values)
    {
        $fields = array_keys($values);
        $setClause = implode(' = ?, ', $fields) . ' = ?';

        $query = "UPDATE {$this->table} SET $setClause WHERE $where";
        
        // Combinar valores e parâmetros da cláusula WHERE
        $bindParams = array_merge(array_values($values), $params);

        return $this->execute($query, $bindParams);
    }

    /**
     * Método responsável por excluir registros da tabela.
     * @param string $where Condição para excluir os registros (ex: "id = ?")
     * @param array $params Parâmetros para a cláusula WHERE
     * @return bool Retorna true se a exclusão for bem-sucedida
     */
    public function delete($where, array $params)
    {
        $query = "DELETE FROM {$this->table} WHERE $where";
        return $this->execute($query, $params);
    }

    /**
     * Método para obter a conexão do banco de dados (caso necessário)
     * @return PDO
     */
    public static function getConnection()
    {
        return self::$connection;
    }
}
