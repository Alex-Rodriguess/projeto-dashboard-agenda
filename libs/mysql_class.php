<?php

class mysql
{

    //    static $ambient = false;
    public $conn = null;

    function __construct($dados_connect)
    {

        //Conecta ao banco e seleciona a base
        $this->connect($dados_connect);
        $this->selectdb($dados_connect['db']);
        //Comandos para padronização UTF8
        //$this->query("SET NAMES 'utf8'");
        //$this->query('SET character_set_connection=utf8');
        //$this->query('SET character_set_client=utf8');
        //$this->query('SET character_set_results=utf8');
    }

    //Função de retorna erro
    public function error($msg)
    {
        die('<div style="font-family:Arial; font-size:13px;border:#CCC 1px solid;background: #F7F7F7; width:500px; margin: 100px auto 0;"><h1 style="margin: 0; font-size:18px; background:#F00; padding:5px;color:#FFF;">ERRO</h1><div style="padding:5px;">' . $msg . '</div></div>');
    }

    //Função de conexão com o banco
    function connect($dados = NULL)
    {
        if ($dados == NULL) {
            $this->error('Nenhum servidor de banco de dados informado.');
        } else {
            $this->conn = mysqli_connect($dados['host'], $dados['user'], $dados['pass']) or $this->error('ERRO: ' . mysqli_connect_error());
        }
    }

    //Função de seleção de banco de dados
    public function selectdb($db = NULL)
    {
        if ($db == NULL) {
            $this->error('Nenhum banco de dados informado.');
        } else {
            $sel = mysqli_select_db($this->conn, $db) or $this->error('ERRO: ' . mysqli_error($this->conn));
        }
    }

    //Anti injection
    public function anti_sqlinj($string)
    {
        //        $string = get_magic_quotes_gpc() ? stripslashes($string) : $string;
        //        $string = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($string) : mysqli_escape_string($this->conn, $string);
        return $string;
    }

    //Envio da query
    public function query($sql)
    {
        $qry = mysqli_query($this->conn, $sql) or $this->error('Erro: ' . mysqli_error($this->conn), 'error');
        return $qry;
    }

    //Insert
    public function insert($tabela, $dados)
    {
        $chaves = array();
        $valores = array();
        foreach ($dados as $key => $value) {
            $chaves[] = $key;
            $valores[] = $this->anti_sqlinj($value);
        }
        $sql = "INSERT INTO " . $tabela . " (`" . implode('`, `', $chaves) . "`) VALUES ('" . implode('\', \'', $valores) . "');";
        $this->query($sql);
    }

    //Update
    public function update($tabela, $dados, $id, $field = 'id')
    {
        foreach ($dados as $key => $value) {
            $query[] = '`' . $key . '`=\'' . $this->anti_sqlinj($value);
        }
        $sql = "UPDATE " . $tabela . " SET " . implode('\', ', $query) . "' WHERE " . $field . "='" . $id . "';";
        $this->query($sql);
    }

    //Delete
    public function delete($tabela, $id, $chave = 'id')
    {
        $sql = "DELETE FROM " . $tabela . " WHERE " . $chave . "='" . $id . "';";
        $this->query($sql);
    }

    //Consulta unica
    public function consult($tabela, $id, $field = "id")
    {
        $sql = "SELECT * FROM " . $tabela . " WHERE " . $field . "='" . $this->anti_sqlinj($id) . "';";
        $rs = $this->query($sql);
        $dados = mysqli_fetch_array($rs, MYSQLI_ASSOC);
        return $dados;
    }

    //Retorna a soma de um campo
    public function sum($table, $campo, $where = null)
    {

        if ($where <> null) {
            $where = " WHERE " . $where;
        }
        $sql = "SELECT SUM(" . $campo . ") as `TOTAL` FROM " . $table . $where . ";";
        $rs = $this->query($sql);

        return mysqli_fetch_array($rs, MYSQLI_ASSOC);
    }

}