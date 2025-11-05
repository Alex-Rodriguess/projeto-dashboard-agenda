<?php

class scccad
{
    private $mysql;

    public function __construct($mysql)
    {
        $this->mysql = $mysql;
    }

    function getCad($id)
    {
        $sql = "SELECT * FROM scccad WHERE `SC_CAD` = '" . $id . "'";
        $rs = $this->mysql->query($sql);
        $dados = mysqli_fetch_array($rs, MYSQLI_ASSOC);
        if (empty($dados['SC_DSC']) or $dados['SC_DSC'] == NULL) {
            $dados['SC_DSC'] = utf8_decode("Cliente não encontrado");
        }
        return utf8_encode($dados['SC_DSC']);
    }

    function getListClient($order = 'SC_CAD', $order_type = 'ASC')
    {
        $sql = "SELECT * FROM scccad WHERE `SC_CLF` = 'C' ORDER BY `" . $order . "` " . $order_type;
        $rs = $this->mysql->query($sql);
        $i = 0;
        while ($dados = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
            $dadosrt[$i]['SC_CAD'] = $dados['SC_CAD'];
            $dadosrt[$i]['SC_NOM'] = $dados['SC_NOM'];
            $i++;
        }
        return $dadosrt;
    }

    function listCli()
    {
        $sql = "SELECT * FROM scccad ORDER BY `SC_NOM` ASC";
        $rs = $this->mysql->query($sql);
        $return = array();
        $i = 0;
        while ($dados = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
            $return[$dados['SC_CAD']] = utf8_encode($dados['SC_DSC']);

            $i++;
        }
        return $return;
    }

    function EditCli($select)
    {
        $sql = "SELECT * FROM scccad ORDER BY `SC_CAD` ASC";
        $rs = $this->mysql->query($sql);

        echo "<option value='' disabled></option>";

        while ($dados = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
            if ($dados['SC_CAD'] == $select) {
                echo "<option value='" . $dados['SC_CAD'] . "' selected>" . utf8_encode($dados['SC_DSC']) . "</option>";
            } else {
                echo "<option value='" . $dados['SC_CAD'] . "'>" . utf8_encode($dados['SC_DSC']) . "</option>";
            }
        }
    }
}