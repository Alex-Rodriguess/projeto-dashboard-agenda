<?php
include("./config.php");
/**
 * @var mysql $mysql
 * @var start $start
 * @var string $user
 * @var string $senha
 */
$valores = null;
$dados_pagprod = null;
//Se as variaveis de login e senha não estão setadas e estão diferentes das
// variaveis, redireciona pra tela de login
if ((!isset($_SESSION["log"]) or $_SESSION["log"] <> $user) and (!isset($_SESSION["senha"]) or $_SESSION["senha"] <> $senha)) {
    header("Location: login.php");
}

// Suporte a "autofilter" via link/form (GET ou POST) - aceita ?autofilter=1 ou POST autofilter=1
$autofilter_used = false;
if ((isset($_GET['autofilter']) && $_GET['autofilter'] == 1) || (isset($_POST['autofilter']) && $_POST['autofilter'] == 1)) {
    $autofilter_used = true;
    // Define valores padrão razoáveis caso não existam
    if (empty($_POST['datainicial'])) {
        $_POST['datainicial'] = date('01/m/Y'); // início do mês
    }
    if (empty($_POST['datafinal'])) {
        $_POST['datafinal'] = date('d/m/Y'); // hoje
    }
    if (empty($_POST['tipo'])) {
        $_POST['tipo'] = 2; // sintético por padrão
    }
    if (empty($_POST['opprod'])) {
        $_POST['opprod'] = isset($_SESSION['opprod']) ? $_SESSION['opprod'] : 27;
    }
    // Se não houver caixa selecionada, pega a primeira disponível
    if (empty($_POST['caixa'])) {
        $rsC = $mysql->query("SELECT CX_COD FROM cxaemp ORDER BY `CX_DSC` ASC LIMIT 1");
        if ($rc = mysqli_fetch_array($rsC, MYSQLI_ASSOC)) {
            $_POST['caixa'] = $rc['CX_COD'];
        }
    }
    // Marca como se o formulário tivesse sido enviado
    $_POST['enviar'] = 'Filtrar';
}

if (!empty($_POST) and $_POST['enviar'] == "Filtrar") {
    //Cria a tabela temporária que será usada.
    $tmpTbl = new tempTable($mysql);
    $tmpTbl->criaTmpTable();

    //Converte data para padrão do banco ano/mês/dia
    $dt_inicial = explode("/", $_POST['datainicial']);
    $dt_inicial = $dt_inicial[2] . $dt_inicial[1] . $dt_inicial[0];
    $dt_where_cxafit = "`FI_DTA` >= '" . $dt_inicial . "' AND";
    $dt_where_sccmov = "`MV_VCT` >= '" . $dt_inicial . "' AND";
    $dt_where_cxaprev = null;
    if (isset($_POST['datafinal']) and !empty($_POST['datafinal'])) {
        $dt_final = explode("/", $_POST['datafinal']);
        $dt_final = $dt_final[2] . $dt_final[1] . $dt_final[0];
        $dt_where_cxafit = "`FI_DTA` >= '" . $dt_inicial . "' AND `FI_DTA` <= '" . $dt_final . "' AND";
        $dt_where_sccmov = "`MV_VCT` >= '" . $dt_inicial . "' AND `MV_VCT` <= '" . $dt_final . "' AND";
        $dt_where_cxaprev = "`PRE_DTA` >= '" . $dt_inicial . "' AND `PRE_DTA` <= '" . $dt_final . "' AND";
    }
    if (isset($_POST['opprod']) && !empty($_POST['opprod'])) {
        $_SESSION['opprod'] = $_POST['opprod'];
    }

    //SELECT do CXAFIT
    $sql_cxafit = "SELECT * FROM CXAFIT WHERE `FI_CXA` = '" . $_POST['caixa'] . "' AND " . $dt_where_cxafit . " `FI_EXT` <> 'e' AND `FI_EXT` <> 'E' ORDER BY `FI_DTA`, `FI_VAL` ASC";
    $rs_cxafit = $mysql->query($sql_cxafit);
    $row_cxafit = mysqli_num_rows($rs_cxafit);
    if ($row_cxafit > 0) {
        while ($lista_cxafit = mysqli_fetch_array($rs_cxafit, MYSQLI_ASSOC)) {
            //Preenche dados em um array que vão ser enviados
            $dt_bk = $lista_cxafit["FI_DTA"];
            $aa = substr($lista_cxafit["FI_DTA"], 0, 4);
            $mm = substr($lista_cxafit["FI_DTA"], 4, 2);
            $dd = substr($lista_cxafit["FI_DTA"], 6, 2);
            $lista_cxafit["FI_DTA"] = strtotime($aa . '-' . $mm . '-' . $dd);
            $w = date('w', $lista_cxafit["FI_DTA"]);
            if ($w == 0 or $w == 6) {
                $dmod = 'w';
            } else {
                $dmod = 'n';
            }
            $lista_cxafit["FI_DTA"] = $dt_bk;
            $dados_cxafit = array(
                "dias" => $lista_cxafit["FI_DTA"],
                "tipo" => 'cxafit',
                "dta_mod" => $dmod,
                "FI_CXA" => $lista_cxafit["FI_CXA"],
                "FI_DTA" => $lista_cxafit["FI_DTA"],
                "FI_LIN" => $lista_cxafit["FI_LIN"],
                "FI_TDC" => $lista_cxafit["FI_TDC"],
                "FI_VAL" => $lista_cxafit["FI_VAL"],
                "FI_VDD" => $lista_cxafit["FI_VDD"],
                "FI_LAN" => $lista_cxafit["FI_LAN"],
                "FI_ADT" => $lista_cxafit["FI_ADT"]
            );
            $mysql->insert("tmp", $dados_cxafit);
        }

    }

    //SELECT das provisões (SCCMOV)
    $sql_sccmov = "SELECT * FROM SCCMOV WHERE " . $dt_where_sccmov . " `MV_PGT` = 0 AND `MV_TIP` >= 1 AND `MV_TIP` <= 9";
    $rs_sccmov = $mysql->query($sql_sccmov);
    $row_sccmov = mysqli_num_rows($rs_sccmov);
    if ($row_sccmov > 0) {
        while ($lista_sccmov = mysqli_fetch_array($rs_sccmov, MYSQLI_ASSOC)) {
            $dt_bk = $lista_sccmov["MV_VCT"];
            $aa = substr($lista_sccmov["MV_VCT"], 0, 4);
            $mm = substr($lista_sccmov["MV_VCT"], 4, 2);
            $dd = substr($lista_sccmov["MV_VCT"], 6, 2);
            $lista_sccmov["MV_VCT"] = strtotime($aa . '-' . $mm . '-' . $dd);
            $w = date('w', $lista_sccmov["MV_VCT"]);
            //Preenche dados em um array que vão ser enviados
            if ($w == 0 or $w == 6) {
                $dados_sccmov = array(
                    "dias" => $dt_bk,
                    "tipo" => 'sccmov',
                    "dta_mod" => "w",
                    "MV_TIP" => NULL,
                    "MV_NUM" => 0.0,
                    "MV_NBC" => NULL,
                    "MV_REP" => 0.0,
                    "MV_VLV" => 0.0,
                    "MV_CAD" => 0.0,
                    "MV_CTA" => 0.0,
                    "MV_CDES" => 0.0,
                    "MV_FAT" => 0.0,
                    "MV_VCT" => 0,
                );
                $mysql->insert("tmp", $dados_sccmov);
                if ($w == 0) {
                    $lista_sccmov["MV_VCT"] = date('Ymd', strtotime($aa . '-' . $mm . '-' . $dd . ' + 1 days'));
                } else {
                    $lista_sccmov["MV_VCT"] = date('Ymd', strtotime($aa . '-' . $mm . '-' . $dd . ' + 2 days'));
                }
                $dmod = 's';
            } else {
                $lista_sccmov["MV_VCT"] = $dt_bk;
                $dmod = 'n';
            }

            $dados_sccmov = array(
                "dias" => $lista_sccmov["MV_VCT"],
                "tipo" => 'sccmov',
                "dta_mod" => $dmod,
                "MV_TIP" => $lista_sccmov["MV_TIP"],
                "MV_NUM" => $lista_sccmov["MV_NUM"],
                "MV_NBC" => $lista_sccmov["MV_NBC"],
                "MV_REP" => $lista_sccmov["MV_REP"],
                "MV_VLV" => $lista_sccmov["MV_VLV"],
                "MV_CAD" => $lista_sccmov["MV_CAD"],
                "MV_CTA" => $lista_sccmov["MV_CTA"],
                "MV_CDES" => $lista_sccmov["MV_CDES"],
                "MV_FAT" => $lista_sccmov["MV_FAT"],
                "MV_VCT" => $lista_sccmov["MV_VCT"],
            );
            $mysql->insert("tmp", $dados_sccmov);
        }
    }

    //SELECT do CXAPREV
    $rs_cxaprev = $mysql->query("SELECT * FROM CXAPREV P, SCCCAD C  WHERE " . $dt_where_cxaprev . " C.SC_CAD = P.PRE_CAD;");
    //    dd("SELECT * FROM CXAPREV P, SCCCAD C  WHERE " . $dt_where_cxaprev . " C.SC_CAD = P.PRE_CAD;");
    $row_cxaprev = mysqli_num_rows($rs_cxaprev);
    if ($row_cxaprev > 0) {
        while ($row = mysqli_fetch_array($rs_cxaprev, MYSQLI_ASSOC)) {
            if (substr_count($row["PRE_DTA"], '-') > 0) {
                $tmp = explode('-', $row['PRE_DTA']);
                $aa = $tmp[0];
                $mm = $tmp[1];
                $dd = $tmp[2];
            } else {
                $aa = substr($row["PRE_DTA"], 0, 4);
                $mm = substr($row["PRE_DTA"], 4, 2);
                $dd = substr($row["PRE_DTA"], 6, 2);
            }

            $dt_bk = $aa . $mm . $dd;
            $row["PRE_DTA"] = strtotime($aa . $mm . $dd);

            $w = date('w', $row["PRE_DTA"]);
            //Preenche dados em um array que vão ser enviados
            if ($w == 0 or $w == 6) {
                $dados_cxaprev = array(
                    "dias" => $dt_bk,
                    "tipo" => 'cxaprev',
                    "dta_mod" => "w",
                    "PRE_CAD" => 0,
                    "PRE_VLR" => 0.0
                );

                $mysql->insert("tmp", $dados_cxaprev);
                if ($w == 0) {
                    $row["PRE_DTA"] = date('Y-m-d', strtotime($row["PRE_DTA"] . ' + 1 days'));
                } else {
                    $row["PRE_DTA"] = date('Y-m-d', strtotime($row["PRE_DTA"] . ' + 2 days'));
                }
                $dmod = 's';
            } else {
                $aa = substr($dt_bk, 0, 4);
                $mm = substr($dt_bk, 5, 2);
                $dd = substr($dt_bk, 6, 2);
                //                $row["PRE_DTA"] = $aa . '-' . $mm . '-' . $dd;
                $row["PRE_DTA"] = $dt_bk;
                $dmod = 'n';

                $dados_cxaprev = array(
                    "dias" => $row["PRE_DTA"],
                    "tipo" => 'cxaprev',
                    "dta_mod" => $dmod,
                    "PRE_DTA" => $row["PRE_DTA"],
                    "PRE_CAD" => $row["SC_DSC"],
                    "PRE_VLR" => $row["PRE_VLR"]
                );
                if (!empty($row["PRE_REP"])) {
                    $dados_cxaprev["PRE_REP"] = $row["PRE_REP"];
                }

                $mysql->insert("tmp", $dados_cxaprev);
            }
        }
    }
    $rs_list = 1;
    $where_tmp = NULL;

    if (!empty($dt_final)) {
        $where_tmp = "AND `dias` <= '" . $dt_final . "'";
    }
    $sql = "SELECT * FROM tmp WHERE `dias` >= '" . $dt_inicial . "' " . $where_tmp . " ORDER BY dias, `FI_LIN` = 0 DESC,`FI_VAL`, `FI_VDD` ASC";
    $rs_tmp = $mysql->query($sql);
    $row_tmp = mysqli_num_rows($rs_tmp);
    $dias = array();
    $i = 0;
    while ($dados = mysqli_fetch_array($rs_tmp, MYSQLI_ASSOC)) {
        //CXAFIT
        $dias[$dados['dias']][$i]['tipo'] = $dados['tipo'];
        $dias[$dados['dias']][$i]['FI_CXA'] = $dados['FI_CXA'];
        $dias[$dados['dias']][$i]['FI_DTA'] = $dados['FI_DTA'];
        $dias[$dados['dias']][$i]['FI_LIN'] = $dados['FI_LIN'];
        $dias[$dados['dias']][$i]['FI_TDC'] = $dados['FI_TDC'];
        $dias[$dados['dias']][$i]['FI_VAL'] = $dados['FI_VAL'];
        $dias[$dados['dias']][$i]['FI_VDD'] = $dados['FI_VDD'];
        $dias[$dados['dias']][$i]['FI_LAN'] = $dados['FI_LAN'];
        $dias[$dados['dias']][$i]['FI_ADT'] = $dados['FI_ADT'];

        //SCCMOV
        $dias[$dados['dias']][$i]['dta_mod'] = $dados['dta_mod'];
        $dias[$dados['dias']][$i]['MV_TIP'] = $dados['MV_TIP'];
        $dias[$dados['dias']][$i]['MV_NUM'] = $dados['MV_NUM'];
        $dias[$dados['dias']][$i]['MV_NBC'] = $dados['MV_NBC'];
        $dias[$dados['dias']][$i]['MV_REP'] = $dados['MV_REP'];
        $dias[$dados['dias']][$i]['MV_VLV'] = $dados['MV_VLV'];
        $dias[$dados['dias']][$i]['MV_CAD'] = $dados['MV_CAD'];
        $dias[$dados['dias']][$i]['MV_CTA'] = $dados['MV_CTA'];
        $dias[$dados['dias']][$i]['MV_CDES'] = $dados['MV_CDES'];
        $dias[$dados['dias']][$i]['MV_FAT'] = $dados['MV_FAT'];
        $dias[$dados['dias']][$i]['MV_VCT'] = $dados['MV_VCT'];

        //CXAPREV
        $dias[$dados['dias']][$i]['PRE_DTA'] = $dados['PRE_DTA'];
        $dias[$dados['dias']][$i]['PRE_REP'] = $dados['PRE_REP'];
        $dias[$dados['dias']][$i]['PRE_CAD'] = $dados['PRE_CAD'];
        $dias[$dados['dias']][$i]['PRE_VLR'] = $dados['PRE_VLR'];
        $i++;
    }
    $start->registerFields('consultabanco', $_POST);
} else {
    //Caso o _POST não for enviado, seta variavel rs_list como NULL para exibir
    //a mensagem de selecionar CAIXA e DATA
    $rs_list = NULL;
    $_POST = $start->getFields('consultabanco');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Consulta Banco</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/cupertino/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/jquery-ui-1.10.4.custom.js"></script>
    <script src="js/i18n/jquery.ui.datepicker-pt-BR.js"></script>
    <script src="js/jquery.maskedinput.min.js"></script>
    <script src="js/jquery.autoNumeric.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/highslide-full.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/highslide.css"/>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <link href="img/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
    <script src="js/scripts.js"></script>
    <script type="text/javascript">
        hs.graphicsDir = 'css/graphics/';
        hs.outlineType = 'rounded-white';
        hs.wrapperClassName = 'draggable-header';
        hs.width = "1000";
        hs.dimmingOpacity = 0.8;
        $(function () {

            $("#datainicial, #datafinal").datepicker({
                dateFormat: 'dd/mm/yy',
                showButtonPanel: true
            });
            $("#datainicial, #datafinal").mask("99/99/9999");
            //$("[rel='popover']").popover();
            $('.nolink').click(function (e) {
                return false;
            });
            $(".nolink").tooltip();


        });
    </script>
</head>
<style>
    .tooltip-inner {
        max-width: 500px !important;
    }

    .nolink {
        border-bottom: #003399 1px dashed;
    }
</style>
<body style="white-space: nowrap;">
<div id="dvLoading"><img src="img/loading2.gif"></div>
<?php
include("menu.php");
?>
<?php if (!empty($autofilter_used)) { ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">Dados carregados automaticamente via Dashboard</div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- style="padding:10px 0 0; background: #f7f7f7;" -->
<div class="container">
    <div class="row">
        <div class="col-sm-12 panel panel-default">
            <?php
            include('forms/bancos.php');
            ?>
        </div>
    </div>
</div>
<div class="container-fluid" style="margin: 0 20px 0">
    <div class="row">
        <?php
        if ($rs_list == NULL) {
            echo '<h3>SELECIONE UM BANCO E UMA DATA</h3>';
        } else {
            if ($row_tmp <= 0) {
                echo '<h3>Nenhum dado encontrado no período selecionado.</h3>';
            } else {
                if ($row_tmp > 0) {
                    $proximo_saldo = 0;
                    $count = 0;
                    foreach ($dias as $dia => $dadosProntos) {
                        if (segunda($dia)) {
                            $_SESSION['inicial'] = $dia;
                            include('consultaBancoResumo.php');
                        }
                        include('consultaBancoTabela.php');
                        $count++;
                    }
                }
            }
        }
        ?>
    </div>
</div>
</body>
</html>
