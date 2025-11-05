<?php

class sccdia
{
    private $mysql;

    public function __construct($mysql)
    {
        $this->mysql = $mysql;
    }

    function getLan($id)
    {
        $sql = "SELECT * FROM sccdia WHERE `DI_LAN` = '" . $id . "'";
        $rs = $this->mysql->query($sql);
        $dados = mysqli_fetch_array($rs, MYSQLI_ASSOC);
        return $dados['DI_CAD'];
    }

    function getFat($id)
    {
        $sql = "SELECT * FROM sccdia WHERE `DI_LAN` = '" . $id . "'";
        $rs = $this->mysql->query($sql);
        $dados = mysqli_fetch_array($rs, MYSQLI_ASSOC);
        return $dados['DI_FAT'];
    }

}