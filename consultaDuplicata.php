<?php
include("config.php");
//Se as variaveis de login e senha não estão setadas e estão diferentes das
// variaveis, redireciona pra tela de login
if ((!isset($_SESSION["log"]) or $_SESSION["log"] <> $user) and (!isset($_SESSION["senha"]) or $_SESSION["senha"] <> $senha)) {
    header("Location: login.php");
}

$scccad = new scccad($mysql);
$estven = new estven($mysql);

if (!empty($_POST) and $_POST['submit'] == "Filtrar") {
    $wh_ven = null;
    $wh_emi = null;
    $wh_pag = null;
    $wh_pagrec = null;
    $wh_cli = null;
    //Data Vencimento Inicial
    if (!empty($_POST['dtaVenIn'])) {
        $wh_ven .= " AND `MV_VCT` >= " . $start->convertDate($_POST['dtaVenIn']);
    }
    //Data Vencimento Final
    if (!empty($_POST['dtaVenFi'])) {
        $wh_ven .= " AND `MV_VCT` <= " . $start->convertDate($_POST['dtaVenFi']);
    }
    //Data Emissão Inicial
    if (!empty($_POST['dtaEmiIn'])) {
        $wh_emi .= " AND `MV_DTE` >= " . $start->convertDate($_POST['dtaEmiIn']);
    }
    //Data Emissão Final
    if (!empty($_POST['dtaEmiFi'])) {
        $wh_emi .= " AND `MV_DTE` <= " . $start->convertDate($_POST['dtaEmiFi']);
    }
    //Data Pagamento Inicial
    if (!empty($_POST['dtaPagIn'])) {
        $wh_emi .= " AND `MV_PGT` >= " . $start->convertDate($_POST['dtaPagIn']);
    }
    //Data Pagamento Final
    if (!empty($_POST['dtaPagFi'])) {
        $wh_emi .= " AND `MV_PGT` <= " . $start->convertDate($_POST['dtaPagFi']);
    }

    if (!empty($_POST['pagrec'])) {
        if ($_POST['pagrec'] == 1) {
            $wh_pagrec = " AND `MV_TIP` >= 5";
        } elseif ($_POST['pagrec'] == 2) {
            $wh_pagrec = " AND `MV_TIP` <= 4";
        } else {
            $wh_pagrec = null;
        }
    } else {
        $_POST['pagrec'] = 0;
    }

    if ($_POST['cliIn'] > 0) {
        $wh_cli .= " AND `MV_CAD` >= " . $_POST['cliIn'];
    }
    if ($_POST['cliFi'] > 0) {
        $wh_cli .= " AND `MV_CAD` <= " . $_POST['cliFi'];
    }

    $orderby = " ORDER BY `MV_VCT`, `MV_DTE`, `MV_PAR`, `MV_LAN` ASC";

    $sql = "SELECT * FROM sccmov WHERE `MV_PGT` = 0 " . $wh_ven . $wh_emi . $wh_pag . $wh_pagrec . $wh_cli . $orderby;
    $_SESSION['sql'] = $sql;
    $rs = $mysql->query($sql);
    $num = mysqli_num_rows($rs);
    $start->registerFields('vencidoseavencer', $_POST);
} else {
    $rs = null;
    $num = null;
    $_POST = $start->getFields('vencidoseavencer');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Consulta Duplicatas</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/cupertino/jquery-ui.min.css">
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="js/jquery.maskedinput.min.js"></script>
    <script src="js/i18n/jquery.ui.datepicker-pt-BR.js"></script>
    <script src="js/bootstrap.js"></script>
    <!-- <script src="js/jquery.validity.min.js"></script> -->
    <script src="js/highslide-full.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <link rel="stylesheet" type="text/css" href="css/highslide.css"/>
    <link href="img/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
    <script type="text/javascript">
        //Configurações da janela iframe do highslide
        hs.graphicsDir = 'css/graphics/';
        hs.outlineType = 'rounded-white';
        hs.wrapperClassName = 'draggable-header';
        hs.width = "1000";
        hs.dimmingOpacity = 0.8;


        $(function () {
            $('#reset').click(function () {
                $('#form1 :input[type="text"]').each(function (i) {
                    $(this).val('');
                });
                $('#form1 :input[type="radio"]').each(function (i) {
                    $(this).prop('checked', false);
                });
                $('#form1 #cliIn, #form1 #cliFi').each(function (i) {
                    $(this).val('0');
                });
                $('#form1 #ambos').prop('checked', true);
            })
            //Faz comparações com as datas de inicio e fim, impedindo selecionar
            //data iniciais meiores que finais e vice e versa
            $("#dtaVenIn").datepicker({
                showButtonPanel: true,
                onClose: function (selectedDate) {
                    $("#dtaVenFi").datepicker("option", "minDate", selectedDate);
                }
            });
            $("#dtaVenFi").datepicker({
                onClose: function (selectedDate) {
                    $("#dtaVenIn").datepicker("option", "maxDate", selectedDate);
                }
            });
            $("#dtaEmiIn").datepicker({
                showButtonPanel: true,
                onClose: function (selectedDate) {
                    $("#dtaEmiFi").datepicker("option", "minDate", selectedDate);
                }
            });
            $("#dtaEmiFi").datepicker({
                onClose: function (selectedDate) {
                    $("#dtaEmiIn").datepicker("option", "maxDate", selectedDate);
                }
            });
            $("#dtaPagIn").datepicker({
                showButtonPanel: true,
                onClose: function (selectedDate) {
                    $("#dtaPagFi").datepicker("option", "minDate", selectedDate);
                }
            });
            $("#dtaPagFi").datepicker({
                onClose: function (selectedDate) {
                    $("#dtaPagIn").datepicker("option", "maxDate", selectedDate);
                }
            });

            //Seta mascara de data para os campos de data
            $("#dtaEmiIn, #dtaEmiFi, #dtaVenIn, #dtaVenFi, #dtaPagIn, #dtaPagFi").mask("99/99/9999");


        });

    </script>
</head>
<body>
<div id="dvLoading"><img src="img/loading2.gif"></div>
<?php
include("menu.php");
?>
<div class="container">
    <div class="row">
        <div class="col-md-12 panel panel-default">
            <?php include("forms/duplicatas.php") ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-2 col-md-offset-10 text-right">
            <form action="consultaDuplicata_print.php" method="post" target="_blank">
                <button class="btn btn-info"><img src="printer.png" alt="print"/> Imprimir</button>
            </form>
        </div>
        <div class="col-md-12">
            <table class="table table-hover table-striped table-condensed">
                <thead>
                <tr>
                    <th>Nº Lançamento</th>
                    <th>Data Emissão</th>
                    <th>Data do Vcto</th>
                    <th>Parcela</th>
                    <th>Nome (Representante)</th>
                    <th class="text-right">Saldo</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $total = 0;
                if ($num == 0) {
                    echo '<tr><th colspan="6" class="text-center">Nenhum dado encontrado.</th</tr>';
                } else {
                    $i = 0;
                    while ($dados = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
                        if ($dados['MV_VLV'] > 0) {
                            $color = 'green';
                            $infos[$i]['MV_LAN'] = $dados['MV_LAN'];
                            $infos[$i]['MV_DTE'] = $dados['MV_DTE'];
                            $infos[$i]['MV_VCT'] = $dados['MV_VCT'];
                            $infos[$i]['MV_PAR'] = $dados['MV_PAR'];
                            $infos[$i]['MV_CAD'] = $dados['MV_CAD'];
                            $infos[$i]['MV_REP'] = $dados['MV_REP'];
                            $infos[$i]['MV_VLV'] = $dados['MV_VLV'];
                            $i++;
                        } elseif ($dados['MV_VLV'] < 0) {
                            $color = 'red';
                            ?>
                            <tr style="color: <?= $color ?>">
                                <th><?= $dados['MV_LAN'] ?></th>
                                <th><?php $date = new DateTime($dados['MV_DTE']);
                                    echo $date->format('d/m/Y'); ?></th>
                                <th><?php $date = new DateTime($dados["MV_VCT"]);
                                    echo $date->format('d/m/Y'); ?></th>
                                <th><?= $dados['MV_PAR'] ?></th>
                                <th><?= $dados['MV_CAD'] . " - " . $scccad->getCad($dados['MV_CAD']);
                                    echo $rep = $dados['MV_REP'] <> 0 ? " (" . $estven->getRep($dados['MV_REP']) . ")" : null; ?></th>
                                <th class="text-right">R$ <?php echo number_format($dados["MV_VLV"], 2, ',', '.') ?></th>
                            </tr>
                            <?php
                        } else {
                            $color = null;
                        }
                        ?>

                        <?php
                        $total += $dados['MV_VLV'];
                    }
                    if (isset($infos)) {
                        foreach ($infos as $chave => $valor) {
                            ?>
                            <tr style="color: green">
                                <th><?= $valor['MV_LAN'] ?></th>
                                <th><?php $date = new DateTime($valor['MV_DTE']);
                                    echo $date->format('d/m/Y'); ?></th>
                                <th><?php $date = new DateTime($valor["MV_VCT"]);
                                    echo $date->format('d/m/Y'); ?></th>
                                <th><?= $valor['MV_PAR'] ?></th>
                                <th><?= $valor['MV_CAD'] . " - " . $scccad->getCad($valor['MV_CAD']);
                                    echo $rep = $valor['MV_REP'] <> 0 ? " (" . $estven->getRep($valor['MV_REP']) . ")" : null; ?></th>
                                <th class="text-right">R$ <?php echo number_format($valor["MV_VLV"], 2, ',', '.') ?></th>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="5" class="text-right">Total:</th>
                    <th><?= "R$ " . number_format($total, 2, ',', '.') ?></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</body>
</html>