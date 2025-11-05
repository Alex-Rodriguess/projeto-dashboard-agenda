<?php

class estven
{
    private $mysql;

    public function __construct($mysql)
    {
        $this->mysql = $mysql;
    }

    function getRep($id)
    {
        $sql = "SELECT * FROM estven WHERE `VN_COD` = '" . $id . "'";
        $rs = $this->mysql->query($sql);
        $dados = mysqli_fetch_array($rs, MYSQLI_ASSOC);

        return !empty($dados) ? utf8_encode($dados['VN_NOM']) : '';
    }

    function listRep()
    {
        $sql = "SELECT * FROM estven ORDER BY `VN_NOM` ASC";
        $rs = $this->mysql->query($sql);
        $return = array();
        $i = 0;
        while ($dados = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
            $return[$i] = $dados['VN_COD'];
            $return[$i] = utf8_encode($dados['VN_NOM']);

            $i++;
        }
        return $return;
    }

    function listaRep()
    {
        $sql = "SELECT * FROM estven ORDER BY `VN_NOM` ASC";
        $rs = $this->mysql->query($sql);
        $return = array();
        $i = 0;
        while ($dados = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
            $return[$dados['VN_COD']] = utf8_encode($dados['VN_NOM']);

            $i++;
        }
        return $return;
    }

    function EditRep($select)
    {
        $sql = "SELECT * FROM estven ORDER BY `VN_NOM` ASC";
        $rs = $this->mysql->query($sql);

        echo "<option value='' ></option>";

        while ($dados = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
            if ($dados['VN_COD'] == $select) {
                echo "<option value='" . $dados['VN_COD'] . "' selected>" . utf8_encode($dados['VN_NOM']) . "</option>";
            } else {
                echo "<option value='" . $dados['VN_COD'] . "'>" . utf8_encode($dados['VN_NOM']) . "</option>";
            }
        }
    }
}
