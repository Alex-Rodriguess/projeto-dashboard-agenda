<?php

class util
{
    function formataData($data, $tipo = 'COBOL')
    {
        switch ($tipo) {
            case 'COBOL' :
                if (preg_match("/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/", $data, $split)) {
                    $data = $split[3] . $split[2] . $split[1];
                }
                break;

            case 'PHP' :
                //$year = substr($data, 0, 4);
                //$month = substr($data, 4, 2);
                //$day = substr($data, 6, 2);
                //if (checkdate($month, $day, $year)) {
                //	$data = $day.'/'.$month.'/'.$year;
                //}
                $data = date("d/m/Y", strtotime($data));

                break;

            default :
                break;
        }
        return $data;
    }

    function formataDinheiro($valor)
    {
        $valor = preg_replace('/[.]/', '', $valor);
        $valor = preg_replace('/[,]/', '.', $valor);
        //$valor = number_format($valor, 2, ',', '');
        return $valor;
    }

}