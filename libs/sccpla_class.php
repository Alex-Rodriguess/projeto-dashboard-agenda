<?php
class sccpla {

    private $mysql;

    public function __construct($mysql)
    {
        $this->mysql = $mysql;
    }

    function getByID($id) {
        $sql = "SELECT * FROM sccpla WHERE `PL_COD` = '".$id."'";
        $rs = $this->mysql->query($sql);
        $dados = mysqli_fetch_array($rs, MYSQLI_ASSOC);
        $dados = $dados['PL_DES'];
        return $dados;
    }

    function getPLCCAS($id) {
        $sql = "SELECT * FROM sccpla WHERE `PL_COD` = '".$id."'";
        $rs = $this->mysql->query($sql);
        $dados = mysqli_fetch_array($rs, MYSQLI_ASSOC);
        $dados = $dados ? $dados['PL_CCAS'] : null;
        return $dados;
    }
}