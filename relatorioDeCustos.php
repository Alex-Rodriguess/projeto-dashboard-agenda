<?php
include("config.php");
$totalFixo = null;
$totalVariavel = null;
//Se as variaveis de login e senha não estão setadas e estão diferentes das
// variaveis, redireciona pra tela de login
if ((!isset($_SESSION["log"]) or $_SESSION["log"] <> $user) and (!isset($_SESSION["senha"]) or $_SESSION["senha"] <> $senha)) {
    header("Location: login.php");
}

$colorF = "#B0C4DE";
$colorV = "#32CD32";
$MSG_NOT_FOUND = "Nenhum dado encontrado no período selecionado";

if (!empty($_POST) and $_POST['submit'] == "Filtrar") {
    $wh_dta = null;

    //Data Inicial
    if (!empty($_POST['dtaIn'])) {
        $wh_dta .= " AND `DI_DTA` >= " . $start->convertDate($_POST['dtaIn']);
    }
    //Data Final
    if (!empty($_POST['dtaFi'])) {
        $wh_dta .= " AND `DI_DTA` <= " . $start->convertDate($_POST['dtaFi']);
    }
    //Vendas no mês
    if (!empty($_POST['venda'])) {
        $venda = $start->convertDate($_POST['venda']);
    }

    $sqlFixo = "SELECT SUM(ABS(D.DI_VAL)) AS SOMA,
                   V.VINOPE_DES AS DES
               FROM SCCDIA AS D
               INNER JOIN VINOPE AS V 
                  ON D.DI_CTA = V.VINOPE_COD
               WHERE V.VINOPE_TIPO = 'F'
                 $wh_dta
               GROUP BY D.DI_CTA";

    $sqlVariavel = "SELECT SUM(ABS(D.DI_VAL)) AS SOMA,
                   V.VINOPE_DES AS DES
               FROM SCCDIA AS D
               INNER JOIN VINOPE AS V 
                  ON D.DI_CTA = V.VINOPE_COD
               WHERE V.VINOPE_TIPO = 'V'
                 $wh_dta
               GROUP BY D.DI_CTA";

    $resultF = $mysql->query($sqlFixo);
    $numF = mysqli_num_rows($resultF);

    $resultV = $mysql->query($sqlVariavel);
    $numV = mysqli_num_rows($resultV);

    $selecionado = true;
} else {
    $selecionado = false;
    $rs = null;
    $numF = null;
    $numV = null;
    //$_POST = $start->getFields('vencidoseavencer');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Custos</title>

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
            padding: 20px;
        }
        .chart-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }
        .chart-col {
            flex: 1;
            min-width: 300px;
        }
        .chart-title {
            font-size: 1.2em;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
            text-align: center;
        }
        @media (max-width: 768px) {
            .chart-col {
                flex: 100%;
            }
        }
    </style>
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
                $('#form1 #ambos').prop('checked', true);
            });
            $('#data, #valor').each(function (i) {
                $(this).val('');
            });
            //Faz comparações com as datas de inicio e fim, impedindo selecionar
            //data iniciais meiores que finais e vice e versa
            $("#dtaIn").datepicker({
                showButtonPanel: true,
                onClose: function (selectedDate) {
                    $("#dtaFi").datepicker("option", "minDate", selectedDate);
                }
            });
            $("#dtaFi").datepicker({
                onClose: function (selectedDate) {
                    $("#dtaIn").datepicker("option", "maxDate", selectedDate);
                }
            });

            //Seta mascara de data para os campos de data
            $("#dtaIn, #dtaFi").mask("99/99/9999");
            $("#valor").setMask('decimal');
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
            <?php include("forms/custos.php") ?>
        </div>
    </div>
</div>

<?php
/* Ajusta para caracteres especiais vindos do MySQL
 * Todas as palavras a seguir devem ter
 * utf8_decode("TEXTO COM ACENTO")
*/
//mb_http_output( "UTF-8" );
//ob_start("mb_output_handler");
//ini_set('default_charset','UTF-8');


if ($selecionado) {
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-hover table-condensed">
                    <thead>
                    <tr>
                        <th>Conta</th>
                        <th>Orçamento</th>
                        <th></th>
                        <th>Conta</th>
                        <th>Orçamento</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    echo '<tr><th colspan="6" class="text-center"><strong>CUSTOS FIXOS</strong></th></tr>';
                    ?>

                    <?php

                    $total = null;
                    if ($numF == 0) {
                        echo "<tr><th colspan='6' class='text-center'>$MSG_NOT_FOUND</th></tr>";
                    } else {
                        $u = 0;
                        $i = 0;
                        while ($dados = mysqli_fetch_array($resultF, MYSQLI_ASSOC)) {
                            if ($u == 0) {
                                echo "<tr bgcolor=$colorF>";
                            }
                            ?>
                            <th><?php echo utf8_encode($dados['DES']) ?></th>
                            <th>
                                <div align="right">R$ <?= number_format($dados['SOMA'], 2, ',', '.') ?></div>
                            </th>
                            <?php
                            $u++;
                            $i++;
                            if ($u == 2) {
                                echo "</tr>";
                                $u = 0;
                            } else {
                                if ($numF == $i) {
                                    echo "<th></th>";
                                    echo "<th></th>";
                                }
                                echo "<th></th>";
                            }
                            ?>
                            <?php
                            $total += $dados['SOMA'];
                        }
                        $totalFixo = $total;

                        echo "<tr bgcolor=$colorF><th colspan='6' class='text-right'><strong>Total R$ " . number_format($total, 2, ',', '.') . "</strong></th></tr>";
                    }
                    ?>
                    </tbody>

                    <tbody>
                    <?php
                    echo "<tr><th colspan='6' class='text-center'><strong>CUSTOS VARIÁVEIS</strong></th></tr>";
                    ?>
                    <?php
                    $total = null;
                    if ($numV == 0) {
                        echo "<tr><th colspan='6' class='text-center'>$MSG_NOT_FOUND</th></tr>";
                    } else {
                        $u = 0;
                        while ($dados = mysqli_fetch_array($resultV, MYSQLI_ASSOC)) {
                            if ($u == 0) {
                                echo "<tr bgcolor=$colorV>";
                            }
                            ?>
                            <th><?php echo utf8_encode($dados['DES']) ?></th>
                            <th>
                                <div align="right">R$ <?= number_format($dados['SOMA'], 2, ',', '.') ?></div>
                            </th>
                            <?php
                            $u += 1;
                            if ($u == 2) {
                                ?>
                                </tr>
                                <?php
                                $u = 0;
                            } else {
                                echo "<th></th>";
                            }
                            $total += $dados['SOMA'];
                        }
                        $totalVariavel = $total;
                        echo "<tr bgcolor=$colorV><th colspan='6' class='text-right'><strong>Total R$ " . number_format($total, 2, ',', '.') . "</strong></th></tr>";
                    }
                    ?>
                    </tbody>
                    <tbody>

                    <?php
                    //Permite acentos
                    $totalfinal = $totalVariavel + $totalFixo;
                    $fardoFixo = $totalFixo / $venda;
                    $fardoVariavel = $totalVariavel / $venda;
                    $totalcustos = $fardoFixo + $fardoVariavel;
                    ?>
                    <!--<tr>
                        <th>Total Custo Fixo:</th>
                        <th><div align='right'>R$ $totalFixo</div></th>
                    </tr>
                    <tr>
                        <th>Total Custo Variável: </th>
                        <th><div align='right'>R$ $totalVariavel</div></th>
                    </tr> -->
                    <tr>
                        <th>Vendas no Mês:</th>
                        <th>
                            <div align='right'>R$ <?= number_format($totalfinal, 2, ',', '.') ?></div>
                        </th>
                    </tr>
                    <tr>
                        <th>Custo Fixo/Fardo:</th>
                        <th>
                            <div align='right'>R$ <?= number_format($fardoFixo, 2, ',', '.') ?></div>
                        </th>
                    </tr>
                    <tr>
                        <th>Custo Variável/Fardo</th>
                        <th>
                            <div align='right'>R$ <?= number_format($fardoVariavel, 2, ',', '.') ?></div>
                        </th>
                    </tr>
                    <tr>
                        <th>Total Custos:</th>
                        <th>
                            <div align='right'>R$ <?= number_format($totalcustos, 2, ',', '.') ?></div>
                        </th>
                    </tr>
                    </tbody>
                </table>

                <!-- Área de Gráficos -->
                <div class="chart-row">
                    <div class="chart-col">
                        <div class="chart-container">
                            <div class="chart-title">Distribuição de Custos</div>
                            <canvas id="distributionChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-col">
                        <div class="chart-container">
                            <div class="chart-title">Comparativo de Custos por Fardo</div>
                            <canvas id="unitCostChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-col">
                        <div class="chart-container">
                            <div class="chart-title">Detalhamento de Custos Fixos</div>
                            <canvas id="fixedCostsChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-col">
                        <div class="chart-container">
                            <div class="chart-title">Detalhamento de Custos Variáveis</div>
                            <canvas id="variableCostsChart"></canvas>
                        </div>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Dados para os gráficos
                    const totalFixo = <?php echo $totalFixo ?: 0 ?>;
                    const totalVariavel = <?php echo $totalVariavel ?: 0 ?>;
                    const fardoFixo = <?php echo $fardoFixo ?: 0 ?>;
                    const fardoVariavel = <?php echo $fardoVariavel ?: 0 ?>;

                    // Gráfico de Distribuição de Custos
                    new Chart(document.getElementById('distributionChart'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Custos Fixos', 'Custos Variáveis'],
                            datasets: [{
                                data: [totalFixo, totalVariavel],
                                backgroundColor: ['#B0C4DE', '#32CD32']
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });

                    // Gráfico de Custos por Fardo
                    new Chart(document.getElementById('unitCostChart'), {
                        type: 'bar',
                        data: {
                            labels: ['Custo por Fardo'],
                            datasets: [{
                                label: 'Fixo',
                                data: [fardoFixo],
                                backgroundColor: '#B0C4DE'
                            }, {
                                label: 'Variável',
                                data: [fardoVariavel],
                                backgroundColor: '#32CD32'
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'R$ ' + value.toFixed(2);
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });

                    // Gráfico de Custos Fixos Detalhados
                    <?php if ($numF > 0): ?>
                    new Chart(document.getElementById('fixedCostsChart'), {
                        type: 'pie',
                        data: {
                            labels: [<?php 
                                $labels = [];
                                $values = [];
                                mysqli_data_seek($resultF, 0);
                                while ($row = mysqli_fetch_array($resultF, MYSQLI_ASSOC)) {
                                    $labels[] = "'" . utf8_encode($row['DES']) . "'";
                                    $values[] = $row['SOMA'];
                                }
                                echo implode(',', $labels);
                            ?>],
                            datasets: [{
                                data: [<?php echo implode(',', $values); ?>],
                                backgroundColor: [
                                    '#8BB5E0', '#6F9FD8', '#5389D0', '#3773C8', '#1B5DC0'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                    <?php endif; ?>

                    // Gráfico de Custos Variáveis Detalhados
                    <?php if ($numV > 0): ?>
                    new Chart(document.getElementById('variableCostsChart'), {
                        type: 'pie',
                        data: {
                            labels: [<?php 
                                $labels = [];
                                $values = [];
                                mysqli_data_seek($resultV, 0);
                                while ($row = mysqli_fetch_array($resultV, MYSQLI_ASSOC)) {
                                    $labels[] = "'" . utf8_encode($row['DES']) . "'";
                                    $values[] = $row['SOMA'];
                                }
                                echo implode(',', $labels);
                            ?>],
                            datasets: [{
                                data: [<?php echo implode(',', $values); ?>],
                                backgroundColor: [
                                    '#32CD32', '#228B22', '#006400', '#008000', '#3CB371'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                    <?php endif; ?>
                });
                </script>
            </div>
        </div>
    </div>
    <?php
}
?>
</body>
</html>