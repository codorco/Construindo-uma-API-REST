<?php

// ============================================================================
class Database
{
    // propriedades
    private $_host;
    private $_database;
    private $_username;
    private $_password;
    private $_return_type;

    // ========================================================================
    public function __construct($cfg_options, $return_type = 'object')
    {
        // define as configurações de conexão
        $this->_host = $cfg_options['host'];
        $this->_database = $cfg_options['database'];
        $this->_username = $cfg_options['username'];
        $this->_password = $cfg_options['password'];

        // define o tipo de retorno
        if (!empty($return_type) && $return_type == 'object') {
            $this->_return_type = PDO::FETCH_OBJ;
        } else {
            $this->_return_type = PDO::FETCH_ASSOC;
        }
    }

    // ========================================================================
    public function execute_query($sql, $parameters = null)
    {
        // executa uma consulta com retorno de resultados

        // conexão
        $connection = new PDO(
            'mysql:host=' . $this->_host . ';dbname=' . $this->_database . ';charset=utf8',
            $this->_username,
            $this->_password,
            array(PDO::ATTR_PERSISTENT => true)
        );

        $results = null;

        // prepara e executa a consulta
        try {

            $db = $connection->prepare($sql);
            if (!empty($parameters)) {
                $db->execute($parameters);
            } else {
                $db->execute();
            }
            $results = $db->fetchAll($this->_return_type);

        } catch (PDOException $err) {

            // fecha a conexão
            $connection = null;

            // retorna erro
            return $this->_result('error', $err->getMessage(), $sql, null, 0, null);
        }

        // fecha a conexão
        $connection = null;

        // retorna resultado
        return $this->_result('success', 'success', $sql, $results, $db->rowCount(), null);
    }

    // ========================================================================
    public function execute_non_query($sql, $parameters = null)
    {
        // executa uma consulta que não retornará resultados

        // conexão
        $connection = new PDO(
            'mysql:host=' . $this->_host . ';dbname=' . $this->_database. ';charset=utf8',
            $this->_username,
            $this->_password,
            array(PDO::ATTR_PERSISTENT => true)
        );

        // inicia transação
        $connection->beginTransaction();

        // prepara e executa a consulta
        try {

            $db = $connection->prepare($sql);
            if (!empty($parameters)) {
                $db->execute($parameters);
            } else {
                $db->execute();
            }

            // último id inserido
            $last_inserted_id = $connection->lastInsertId();

            // finaliza transação
            $connection->commit();

        } catch (PDOException $err) {

            // desfaz todas as operações SQL em caso de erro
            $connection->rollBack();

            // fecha a conexão
            $connection = null;

            // retorna erro
            return $this->_result('error', $err->getMessage(), $sql, null, 0, null);
        }

        $connection = null;

        // retorna resultado
        return $this->_result('success', 'success', $sql, null, $db->rowCount(), $last_inserted_id);
    }

    // ========================================================================
    private function _result($status, $message, $sql, $results, $affected_rows, $last_id)
    {
        $tmp = new stdClass();
        $tmp->status = $status;
        $tmp->message = $message;
        $tmp->query = $sql;
        $tmp->results = $results;
        $tmp->affected_rows = $affected_rows;
        $tmp->last_id = $last_id;
        return $tmp;
    }
}
