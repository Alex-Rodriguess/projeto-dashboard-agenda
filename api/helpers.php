<?php
if (!function_exists('dd')) {
function dd()
{
    array_map(function ($x) {
        var_dump($x);
    }, func_get_args());
    die;
}
}
if (!function_exists('adicionaDias')) {
    function adicionaDias($data, $dias)
    {
        if (substr_count($data, '-') > 0) {
            $tmp = explode('-', $data);
            $y = $tmp[0];
            $m = $tmp[1];
            $d = $tmp[2];
        } else {
            $y = substr($data, 0, 4);
            $m = substr($data, 4, 2);
            $d = substr($data, 6, 2);
        }

        $resData = date('d/m/Y', mktime(0, 0, 0, $m, (int)$d + $dias, $y));
        $y = substr($resData, 6, 4);
        $m = substr($resData, 3, 2);
        $d = substr($resData, 0, 2);

        return $y . $m . $d;
        //return $resData;
    }
}

/**
 * Traz o dia da semana para qualquer data informada
 */
if (!function_exists('segunda')) {
    function segunda($data)
    {
        if (substr_count($data, '-') > 0) {
            $tmp = explode('-', $data);
            $ano = $tmp[0];
            $mes = $tmp[1];
            $dia = $tmp[2];
        } else {
            $ano = substr($data, 0, 4);
            $mes = substr($data, 4, 2);
            $dia = substr($data, 6, 2);
        }
        $diasemana = date("w", mktime(0, 0, 0, $mes, $dia, $ano));
        $isSegunda = false;
        if ($diasemana == '1') {
            $isSegunda = true;
        }
        return $isSegunda;
    }
}

if (!function_exists('mysqli_result')) {
    function mysqli_result($res, $row = 0, $col = 0)
    {
        $numrows = mysqli_num_rows($res);
        if ($numrows && $row <= ($numrows - 1) && $row >= 0) {
            mysqli_data_seek($res, $row);
            $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
            if (isset($resrow[$col])) {
                return $resrow[$col];
            }
        }
        return false;
    }
}