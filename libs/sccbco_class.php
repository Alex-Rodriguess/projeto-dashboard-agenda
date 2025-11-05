<?php

class sccbco
{
    private $mysql;

    public function __construct($mysql)
    {
        $this->mysql = $mysql;
    }

    function getBco($id)
    {
        $sql = "SELECT * FROM sccbco WHERE `BC_COD` = '" . $id . "'";
        $rs = $this->mysql->query($sql);
        $dados = mysqli_fetch_array($rs, MYSQLI_ASSOC);
        if (empty($dados['BC_DES']) or $dados['BC_DES'] == NULL) {
            $dados['BC_DES'] = utf8_decode("Banco não encontrado");
        }
        return utf8_encode($dados['BC_DES']);
    }

}