<?php
include("config.php");
//Se as variaveis de login e senha não estão setadas e estão diferentes das
// variaveis, redireciona pra tela de login
if ((!isset($_SESSION["log"]) or $_SESSION["log"] <> $user) and (!isset($_SESSION["senha"]) or $_SESSION["senha"] <> $senha)) {
    header("Location: login.php");
}

if (isset($_POST['submit']) AND $_POST['submit'] == 'Salvar') {

    $data = $util->formataData($_POST['data']);
    $valor = $util->formataDinheiro($_POST['valor']);

    $dados = array(
        'PRE_DTA' => $data,
        'PRE_CAD' => $_POST['cliente'],
        'PRE_VLR' => $valor
    );
    if (!empty($_POST['representante'])) {
        $dados['PRE_REP'] = $_POST['representante'];
    }

    $mysql->insert('cxaprev', $dados);
}

$estven = new estven($mysql);
$scccad = new scccad($mysql);
?>
<!DOCTYPE html>
<head>
    <title>Agendamento de Previsões</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/cupertino/jquery-ui.min.css">
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="js/jquery.meio.mask.js"></script>
    <script src="js/i18n/jquery.ui.datepicker-pt-BR.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/highslide-full.min.js"></script>

    <link rel="stylesheet" type="text/css" href="css/highslide.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <link href="img/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
    <script type="text/javascript">
        hs.graphicsDir = 'css/graphics/';
        hs.outlineType = 'rounded-white';
        hs.wrapperClassName = 'draggable-header';
        hs.width = "300";
        hs.dimmingOpacity = 0.8;
        hs.Expander.prototype.onAfterClose = function () {
            window.location.reload();
        };
        $(function () {
            $('#reset').click(function () {
                //$('#data, #valor').each(function(i) {
                //	$(this).val('');
                //});
                $('select').each(function (i) {
                    $(this).val('0');
                });
            });

            $("#data, .dataEdit").datepicker({
                showButtonPanel: true
            });

            //$("#data").setMask('date');
            $("#valor").setMask('signed-decimal');
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
        <div class="col-md-12">
            <table id="listagem" cellspacing="1" class="table-bordered table-condensed table-striped table-hover" style="width: 100%">
                <tbody>
                <?php
                include("forms/previsoes.php");
                ?>
                </tbody>
            </table>
            <table id="listagem" cellspacing="1" class="table-bordered table-condensed table-striped table-hover" style="width: 100%">
                <thead>
                <tr>
                    <th class="text-center">Data</th>
                    <th>Representante</th>
                    <th>Cliente</th>
                    <th>Valor</th>
                    <th class="text-center">Editar</th>
                    <th class="text-center">Excluir</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT * FROM cxaprev ORDER BY `PRE_DTA` DESC, `PRE_VLR` > 0";
                $rs = $mysql->query($sql);
                while ($dados = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
                    if ($dados['PRE_VLR'] > 0) {
                        $color = 'green';
                    } elseif ($dados['PRE_VLR'] < 0) {
                        $color = 'red';
                    } else {
                        $color = null;
                    }

                    ?>
                    <tr style="color: <?= $color ?>">
                        <td class="text-center"><?= $util->formataData($dados['PRE_DTA'], 'PHP'); ?></td>
                        <td><?= $estven->getRep($dados['PRE_REP']) ?></td>
                        <td><?= $scccad->getCad($dados['PRE_CAD']) ?></td>
                        <td>R$ <?= number_format($dados['PRE_VLR'], '2', ',', '.') ?></td>
                        <td class="text-center"><a href="agendaPrevisoesEdit.php?id=<?= $dados['PRE_COD'] ?>" class="btn btn-info btn-sm"><i class="glyphicon glyphicon-pencil"></i></a></td>
                        <td class="text-center"><a href="agendaPrevisoesdel.php?id=<?= $dados['PRE_COD'] ?>" class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-trash"></i></a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>