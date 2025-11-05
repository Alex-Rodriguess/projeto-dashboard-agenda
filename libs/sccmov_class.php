<?php

class sccmov
{
    private $mysql;

    public function __construct($mysql)
    {
        $this->mysql = $mysql;
    }

    function getNBC($id)
    {
        $sql = "SELECT * FROM sccmov WHERE `MV_LAN` = '" . $id . "'";
        $rs = $this->mysql->query($sql);
        $dados = mysqli_fetch_array($rs, MYSQLI_ASSOC);
        return $dados['MV_NBC'];
    }

}