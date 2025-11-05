<?php

class cxafit
{
    private $mysql;

    public function __construct($mysql)
    {
        $this->mysql = $mysql;
    }

    function sumByOP($id, $dta)
    {
        $sql = "SELECT SUM(`FI_VAL`) as `FI_VAL`, SUM(`FI_VDD`) as `FI_VDD` FROM cxafit WHERE `FI_DOC`='" . $id . "' AND `FI_DTA` = '" . $dta . "';";
        $rs = $this->mysql->query($sql);
        $dados = mysqli_fetch_array($rs, MYSQLI_ASSOC);
        $dados = $dados['FI_VAL'] - $dados['FI_VDD'];
        return $dados;
    }
}