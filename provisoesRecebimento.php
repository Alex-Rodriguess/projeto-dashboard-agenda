<?php
include("config.php");
// Suporte a autofilter via link/form (GET ou POST)
if ((isset($_GET['autofilter']) && $_GET['autofilter'] == 1) || (isset($_POST['autofilter']) && $_POST['autofilter'] == 1)) {
    if (empty($_GET['id']) && empty($_POST['id'])) {
        $_REQUEST['id'] = date('Y-m-d');
    }
    if (!empty($_POST['id'])) {
        $_REQUEST['id'] = $_POST['id'];
    } elseif (!empty($_GET['id'])) {
        $_REQUEST['id'] = $_GET['id'];
    }
}
$date = date_parse(isset($_REQUEST["id"]) ? $_REQUEST["id"] : '');
if (isset($_GET["id"]) and $date["year"] == true and $date["month"] == true and $date["day"] == true) {
    $sql = "SELECT * FROM sccmov WHERE `MV_VCT` = '" . $_GET["id"] . "' AND `MV_PGT` = 0 AND `MV_TIP` >= 5 AND `MV_TIP` <= 9 AND `MV_CDES` <> 1 ORDER BY `MV_VCT`";
    $rs = $mysql->query($sql);
    $nums = mysqli_num_rows($rs);
} else {
    $rs = NULL;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Consulta Providencias a Receber</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/cupertino/jquery-ui.min.css">
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/jquery-ui-1.10.4.custom.js"></script>
    <script src="js/i18n/jquery.ui.datepicker-pt-BR.js"></script>
    <script src="js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <link href="img/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
    <style type="text/css">
        body {
            padding-top: 20px;
        }
    </style>
</head>
<body style="white-space: nowrap;">
<div class="row">
    <div class="col-sm-12 text-right">
        <a class="btn btn-info" href="provisoesRecebimento_print.php?id=<?= $_GET['id'] ?>" target="_blank" title="Imprimir"><img src="printer.png" alt="Imprimir" title="Imprimir"/> Imprimir</a>
    </div>
</div>
<?php if (isset($_GET['autofilter']) && $_GET['autofilter'] == 1) { ?>
    <div class="container"><div class="alert alert-info">Dados carregados automaticamente via Dashboard</div></div>
<?php } ?>
<?php
if ($rs == NULL or $nums == 0) {
    ?>
    <div class="text-center alert-danger">Nenhum registro encontrado</div>
    <?php
} else {
    ?>
    <table cellspacing="1" class="table table-condensed table-striped table-hover">

        <tbody>
        <?php
        $total = 0;

        //					$estven = new estven($mysql);
        //				$estven->getRep($dados["MV_REP"])
        $scccad = new scccad($mysql);
        $sccbco = new sccbco($mysql);
        while ($dados = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
            //Soma para a variavel TOTAL
            $total += $dados["MV_VLV"];
            ?>
            <tr class="initialism">
                <th><?php echo $dados["MV_NUM"] ?></th>
                <th><?php echo $dados["MV_PAR"] ?></th>
                <th><?php $date = new DateTime($dados["MV_VCT"]);
                    echo $date->format('d/m/Y'); ?></th>
                <th><?php
                    if ($dados["MV_PGT"] == 0) {
                        echo "Pendente de pagamento";
                    } else {
                        echo $date = new DateTime($dados["MV_PGT"]);
                        echo $date->format('d/m/Y');
                    }
                    ?></th>
                <th><?= $scccad->getCad($dados["MV_CAD"]) ?></th>
                <th><?= $sccbco->getBco($dados["MV_BCO"]) ?></th>
                <th>R$ <?php echo number_format($dados["MV_VLV"], 2, ',', '.') ?></th>
            </tr>
            <?php
        }
        ?>
        </tbody>
        <thead>
        <tr>
            <th>Nº da Duplicata</th>
            <th>Parcela</th>
            <th>Data Vencimento</th>
            <th>Data Pago</th>
            <th>Cliente</th>
            <th>Banco</th>
            <th>Valor</th>
        </tr>

        <tr>
            <th colspan="6" class=" text-right">Total:</th>
            <th>R$ <?php echo number_format($total, 2, ',', '.')
                ?></th>
        </tr>
        </thead>
        <tfoot>
        <tr class="<?php echo $class_css ?>">
            <th colspan="6" class="text-right">Total:</th>
            <th>R$ <?php echo number_format($total, 2, ',', '.') ?></th>
        </tr>
        <tr>
            <th>Nº da Duplicata</th>
            <th>Parcela</th>
            <th>Data Vencimento</th>
            <th>Data Pago</th>
            <th>Cliente</th>
            <th>Banco</th>
            <th>Valor</th>
        </tr>
        </tfoot>
    </table>
    <?php
}
?>
</body>
</html>