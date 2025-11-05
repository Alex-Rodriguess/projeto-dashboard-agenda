<?php

class start
{
    private $mysql;

    function __construct($mysql)
    {
        $this->mysql = $mysql;
        $this->createTables();
    }

    function createTables()
    {
        //TODO
        //Criar tabelas viewsccmov e tabelas que serão utilizadas
        if (!$this->checkTableExists('memForms')) {
            $this->createTable("memForms", "
					`id` INT NULL AUTO_INCREMENT,
					`form` VARCHAR(100) NULL,
					`vals` TEXT NULL,
					UNIQUE INDEX `uq_form` (`form`),
					INDEX `id_pk` (`id`) ");
        }
    }

    //Checa se existe a tabela passada por parametro
    function checkTableExists($table)
    {
        $sql = "SHOW TABLES LIKE '" . $table . "'";
        $rs = $this->mysql->query($sql);
        if (mysqli_num_rows($rs) == 1) {
            return true;
        } else {
            return false;
        }
    }

    //Cria uma tabela do tipo View
    function createView($table, $sql)
    {
        $sql = "CREATE DEFINER=`root`@`localhost` VIEW `" . $table . "` AS " . $sql . ";";
        $rs = $this->mysql->query($sql);

    }

    //Cria uma tabela normal
    function createTable($table, $sql)
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . $table . " (
			" . $sql . "
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $rs = $this->mysql->query($sql);
    }

    //Registra no banco a memorização dos campos dos formularios serializados
    function registerFields($form, $dados)
    {
        unset($dados['submit']);
        $dados = serialize($dados);

        $sql = "INSERT INTO memforms (form, vals) VALUES ('" . $form . "', '" . $dados . "')
				ON DUPLICATE KEY UPDATE
				vals = VALUES(vals);";
        $rs = $this->mysql->query($sql);
    }

    //Retorna os dados do form serealizados e deserealiza
    function getFields($table)
    {
        $sql = "SELECT * FROM memForms WHERE form = '" . $table . "';";
        $rs = $this->mysql->query($sql);
        if (mysqli_num_rows($rs) > 0) {
            $dados = mysqli_fetch_array($rs, MYSQLI_ASSOC);
            $dados = $dados['vals'];
            //            $dados = unserialize(mysql_result($rs, 0, 'vals'));
        } else {
            $dados = null;
        }
        return $dados;
    }

    //Converte datas do padrão dd/mm/yyyy para yyyymmdd
    function convertDate($data)
    {
        if (strstr($data, '/')) {
            $data = implode("", array_reverse(explode("/", $data)));
        }
        return $data;
    }
}