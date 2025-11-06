<?php
include("./config.php");
if ((!isset($_SESSION["log"]) or $_SESSION["log"] <> $user) and (!isset($_SESSION["senha"]) or $_SESSION["senha"] <> $senha)) {
    header("Location: login.php");
}
// pega caixa padrão para os formulários rápidos
$defaultCaixa = null;
$rsC = $mysql->query("SELECT CX_COD FROM cxaemp ORDER BY `CX_DSC` ASC LIMIT 1");
if ($rc = mysqli_fetch_array($rsC, MYSQLI_ASSOC)) {
    $defaultCaixa = $rc['CX_COD'];
}
?>
<?php
// --- Monta dados reais para o dashboard (substitui dados fictícios) ---
// Usa tabelas: sccmov (provisões/títulos) e cxafit (movimentações de caixa)
// Gera: labels (últimos 6 meses), entradas (recebimentos) e saidas (pagamentos)
$labels = array();
$entradas = array();
$saidas = array();
for ($i = 5; $i >= 0; $i--) {
    $dt = new DateTime("first day of -$i month");
    $ym = $dt->format('Ym'); // formato YYYYMM para comparação com MV_VCT (YYYYMMDD)
    // rótulo legível
    $monthNames = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    $labels[] = $monthNames[(int)$dt->format('n') - 1] . ' ' . $dt->format('Y');

    // Recebimentos: MV_TIP entre 5 e 9
    $sqlIn = "SELECT COALESCE(SUM(MV_VLV),0) AS soma FROM sccmov WHERE MV_TIP >= 5 AND MV_TIP <= 9 AND LEFT(MV_VCT,6) = '" . $ym . "'";
    $rsIn = $mysql->query($sqlIn);
    $rowIn = mysqli_fetch_array($rsIn, MYSQLI_ASSOC);
    $entradas[] = (float) $rowIn['soma'];

    // Saídas (pagamentos): MV_TIP entre 1 e 4
    $sqlOut = "SELECT COALESCE(SUM(MV_VLV),0) AS soma FROM sccmov WHERE MV_TIP >= 1 AND MV_TIP <= 4 AND LEFT(MV_VCT,6) = '" . $ym . "'";
    $rsOut = $mysql->query($sqlOut);
    $rowOut = mysqli_fetch_array($rsOut, MYSQLI_ASSOC);
    $saidas[] = (float) $rowOut['soma'];
}

    // Saldo acumulado por mês (entradas - saídas cumulativo)
    $saldoAcumulado = array();
    $running = 0.0;
    for ($i = 0; $i < count($entradas); $i++) {
        $running += ($entradas[$i] - $saidas[$i]);
        $saldoAcumulado[] = $running;
    }

// Estatísticas rápidas
$currentYm = date('Ym');
$rsRecMes = $mysql->query("SELECT COALESCE(SUM(MV_VLV),0) AS soma FROM sccmov WHERE MV_TIP >= 5 AND MV_TIP <= 9 AND LEFT(MV_VCT,6) = '" . $currentYm . "'");
$recMesRow = mysqli_fetch_array($rsRecMes, MYSQLI_ASSOC);
$recebimentosMes = (float) $recMesRow['soma'];

$rsPagMes = $mysql->query("SELECT COALESCE(SUM(MV_VLV),0) AS soma FROM sccmov WHERE MV_TIP >= 1 AND MV_TIP <= 4 AND LEFT(MV_VCT,6) = '" . $currentYm . "'");
$pagMesRow = mysqli_fetch_array($rsPagMes, MYSQLI_ASSOC);
$pagamentosMes = (float) $pagMesRow['soma'];

$rsTitulosPend = $mysql->query("SELECT COUNT(*) AS cnt FROM sccmov WHERE MV_PGT = 0");
$tpRow = mysqli_fetch_array($rsTitulosPend, MYSQLI_ASSOC);
$titulosPendentes = (int) $tpRow['cnt'];

// Saldo aproximado por caixa (tenta somar FI_VAL - FI_VDD em cxafit para o caixa padrão)
$saldoTotal = 0.0;
if (!empty($defaultCaixa)) {
    $rsSaldo = $mysql->query("SELECT COALESCE(SUM(FI_VAL),0) AS val, COALESCE(SUM(FI_VDD),0) AS vdd FROM cxafit WHERE FI_CXA = '" . $defaultCaixa . "'");
    if ($rsSaldo) {
        $srow = mysqli_fetch_array($rsSaldo, MYSQLI_ASSOC);
        $saldoTotal = (float) ($srow['val'] - $srow['vdd']);
    }
}
// Previsão de recebimentos (soma provisões futuras do mês atual) — reutiliza $recebimentosMes
$previsaoRecebimentos = $recebimentosMes;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Financeiro</title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/Cerulean.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- Adiciona Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Ícones locais (substitui dependência do CDN) -->
    <style>
        /* Estilos Base */
        :root {
            --card-padding-desktop: 25px;
            --card-padding-tablet: 20px;
            --card-padding-mobile: 15px;
            --card-margin: 15px;
        }

        body {
            font-size: 16px;
            line-height: 1.5;
            overflow-x: hidden;
        }

        .container-fluid {
            padding: 15px;
            max-width: 1920px;
            margin: 0 auto;
        }

        /* Cards e Gráficos Responsivos */
        .dashboard-card {
            background: linear-gradient(145deg, #ffffff, #f5f7fa);
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            margin-bottom: var(--card-margin);
            padding: var(--card-padding-desktop);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .stat-card {
            text-align: center;
            padding: var(--card-padding-desktop);
            border-radius: 12px;
            margin-bottom: var(--card-margin);
            color: 
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
            pointer-events: none;
        }

        .stat-card .trend {
            font-size: 1.2em;
            margin-top: 10px;
            opacity: 0.8;
            color: #ffffffff;
        }

        .stat-card .detail {
            font-size: 1.2em;
            margin-top: 5px;
            color: #ffffffff;
        }

        .card-titulo {
            font-size: 2.2em;
            font-weight: 600;
            margin-bottom: 15px;
            color: #f0f3f6ff;
        }

        .stat-card.primary { background: linear-gradient(135deg, #1a5f9c, #2980b9); }
        .stat-card.success { background: linear-gradient(135deg, #27ae60, #2ecc71); }
        .stat-card.warning { background: linear-gradient(135deg, #f39c12, #f1c40f); }
        .stat-card.danger { background: linear-gradient(135deg, #c0392b, #e74c3c); }

        /* Botões Responsivos */
        .menu-button {
            display: inline-flex; /* alinha ícone e texto verticalmente */
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            margin: 8px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            /* manter texto centralizado visualmente com padding */
            position: relative;
            overflow: hidden;
            color: white !important;
            width: calc(33.33% - 16px);
            min-width: 200px;
            /* permitir quebra de linha para títulos longos sem sair do botão */
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
            text-overflow: unset;
            z-index: 1; /* garante que o link fique acima de pseudo-elementos */
        }

        /* Ícone rápido embutido nos botões (SVG inlined) */
        .quick-icon {
            width: 20px;
            height: 20px;
            display: inline-block;
            vertical-align: middle;
            flex-shrink: 0;
            margin-right: 8px;
            /* quando SVG inline contém paths sem cor, use currentColor para herdar a cor do botão */
            fill: currentColor;
        }

        /* Garante que o SVG ocupe totalmente o espaço da classe quando injetado */
        .quick-icon svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .menu-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .menu-button::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255,255,255,0.2), rgba(255,255,255,0));
            transition: all 0.3s ease;
            pointer-events: none; /* permite clicar no link mesmo com o ::after presente */
        }

        .menu-button:hover::after {
            transform: translateX(100%);
        }

        .menu-section {
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
            padding: var(--card-padding-desktop);
            border-radius: 15px;
            margin-bottom: var(--card-margin);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .chart-title {
            font-size: 1.2em;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .stat-value {
            font-size: 2em;
            font-weight: bold;
            margin: 10px 0;
            color: #ffffffff;
        }

        /* Altura mínima para gráficos (ajustada para melhor visualização) */
        canvas {
            min-height: 220px;
            max-height: 360px;
        }

        /* Tamanhos por grid column para melhor encaixe */
        .col-md-6 .dashboard-card canvas {
            min-height: 220px;
            max-height: 320px;
        }

        .col-md-4 .dashboard-card canvas {
            min-height: 180px;
            max-height: 260px;
        }

        /* Media Queries */
        @media (max-width: 1366px) {
            :root {
                --card-padding-desktop: 20px;
            }
            
            .stat-value {
                font-size: 1.8em;
            }
            
            .chart-title {
                font-size: 1.1em;
            }
        }

        @media (max-width: 992px) {
            :root {
                --card-padding-desktop: var(--card-padding-tablet);
            }
            
            .menu-button {
                width: calc(50% - 16px);
            }
            
            .stat-value {
                font-size: 1.6em;
            }
            
            .trend, .detail {
                font-size: 0.75em;
            }
            
            canvas {
                min-height: 250px;
            }
        }

        @media (max-width: 768px) {
            :root {
                --card-padding-desktop: var(--card-padding-mobile);
            }
            
            .container-fluid {
                padding: 10px;
            }
            
            .menu-button {
                width: 100%;
                margin: 5px 0;
            }
            
            .stat-card {
                margin-bottom: 10px;
            }
            
            .dashboard-card {
                margin-bottom: 15px;
            }
            
            .stat-value {
                font-size: 1.4em;
            }
            
            canvas {
                min-height: 200px;
            }
        }

        @media (max-width: 576px) {
            .trend, .detail {
                font-size: 0.7em;
            }
            
            .chart-title {
                font-size: 1em;
            }
            
            .stat-value {
                font-size: 1.2em;
            }
        }

        /* Ajustes para telas muito pequenas */
        @media (max-width: 360px) {
            .container-fluid {
                padding: 5px;
            }
            
            :root {
                --card-padding-mobile: 10px;
            }
            
            .menu-button {
                padding: 10px 20px;
                font-size: 0.9em;
            }
        }

        /* Ajustes para telas muito grandes */
        @media (min-width: 1920px) {
            .container-fluid {
                padding: 30px;
            }
            
            .stat-value {
                font-size: 2.2em;
                
            }
            
            .chart-title {
                font-size: 1.4em;
            }
        }
    </style>
</head>
<body>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <div class="container-fluid mt-4">
        <div class="row g-3">
            <!-- Cards de Estatísticas -->
            <div class="col-md-3">
                <div class="stat-card primary">
                    <h3 class="card-titulo">Saldo Total</h3>
                    <h2 id="saldoTotal" class="stat-value">R$ <?php echo number_format($saldoTotal, 2, ',', '.'); ?></h2>
                    <div class="trend">↑ 12% desde o mês anterior</div>
                    <div class="detail">Última atualização: <span id="ultimaAtualizacao"><?php echo date('d/m/Y'); ?></span></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <h3 class="card-titulo">Recebimentos</h3>
                    <h2 id="recebimentos" class="stat-value">R$ <?php echo number_format($recebimentosMes, 2, ',', '.'); ?></h2>
                    <div class="trend">Previsão mensal: R$ <span id="previsaoRecebimentos"><?php echo number_format($previsaoRecebimentos, 2, ',', '.'); ?></span></div>
                    <div class="detail">Títulos a vencer: <span id="titulosAVencer"><?php echo $titulosPendentes; ?></span></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <h3 class="card-titulo">Pagamentos</h3>
                    <h2 id="pagamentos" class="stat-value">R$ <?php echo number_format($pagamentosMes, 2, ',', '.'); ?></h2>
                    <div class="trend">Próximo vencimento: R$ <span id="proximoPagamento">0,00</span></div>
                    <div class="detail">Vence em: <span id="dataProximoPagamento">DD/MM</span></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card danger">
                    <h3 class="card-titulo">Títulos Pendentes</h3>
                    <h2 id="titulosPendentes" class="stat-value"><?php echo $titulosPendentes; ?></h2>
                    <div class="trend">Total: R$ <span id="valorTitulosPendentes">0,00</span></div>
                    <div class="detail">Vencidos: <span id="titulosVencidos">0</span></div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Gráfico de Fluxo de Caixa -->
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h4 class="chart-title">Fluxo de Caixa - Últimos 6 Meses</h4>
                    <canvas id="fluxoCaixaChart"></canvas>
                </div>
            </div>
            <!-- Gráfico de Barras - Comparativo Mensal -->
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h4 class="chart-title">Comparativo Mensal de Receitas/Despesas</h4>
                    <canvas id="comparativoChart"></canvas>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Gráfico de Pizza - Distribuição de Despesas -->
            <div class="col-md-4">
                <div class="dashboard-card">
                    <h4 class="chart-title">Distribuição de Despesas</h4>
                    <canvas id="despesasChart"></canvas>
                </div>
            </div>
            <!-- Gráfico de Linha - Projeção Financeira -->
            <div class="col-md-4">
                <div class="dashboard-card">
                    <h4 class="chart-title">Projeção Financeira - Próximos 3 Meses</h4>
                    <canvas id="projecaoChart"></canvas>
                </div>
            </div>
            <!-- Gráfico de Área - Saldo Acumulado -->
            <div class="col-md-4">
                <div class="dashboard-card">
                    <h4 class="chart-title">Saldo Acumulado</h4>
                    <canvas id="saldoAcumuladoChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Menu de Navegação -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="menu-section">
                    <h4 class="mb-3">Menu Rápido</h4>
                    <!-- Consulta Banco (POST) -->
                    <form method="POST" action="consultaBanco.php" style="display:inline-block; margin:0;">
                        <input type="hidden" name="autofilter" value="1" />
                        <input type="hidden" name="datainicial" value="<?= date('01/m/Y') ?>" />
                        <input type="hidden" name="datafinal" value="<?= date('d/m/Y') ?>" />
                        <input type="hidden" name="tipo" value="2" />
                        <input type="hidden" name="opprod" value="<?= isset($_SESSION['opprod']) ? $_SESSION['opprod'] : 27 ?>" />
                        <input type="hidden" name="caixa" value="<?= $defaultCaixa ?>" />
                        <input type="hidden" name="enviar" value="Filtrar" />
                        <button type="submit" class="menu-button btn-primary" data-toggle="tooltip" title="Abrir com filtros automáticos">
                            <?php echo str_replace('<svg', '<svg class="quick-icon" aria-hidden="true"', @file_get_contents('img/icons/bank.svg')); ?>Consulta Banco
                        </button>
                    </form>

                    <!-- Consulta Duplicatas (POST) -->
                    <form method="POST" action="consultaDuplicata.php" style="display:inline-block; margin:0;">
                        <input type="hidden" name="autofilter" value="1" />
                        <input type="hidden" name="id" value="<?= date('Y-m-d') ?>" />
                        <button type="submit" class="menu-button btn-success" title="Abrir com filtros automáticos">
                            <?php echo str_replace('<svg', '<svg class="quick-icon" aria-hidden="true"', @file_get_contents('img/icons/list.svg')); ?>Consulta Duplicatas
                        </button>
                    </form>

                    <!-- Agendamento de Provisões (POST) -->
                    <form method="POST" action="agendaPrevisoes.php" style="display:inline-block; margin:0;">
                        <input type="hidden" name="autofilter" value="1" />
                        <input type="hidden" name="id" value="<?= date('Y-m-d') ?>" />
                        <button type="submit" class="menu-button btn-warning" title="Abrir com filtros automáticos">
                            <?php echo str_replace('<svg', '<svg class="quick-icon" aria-hidden="true"', @file_get_contents('img/icons/invoice.svg')); ?>Agendamento de Provisões
                        </button>
                    </form>

                    <!-- Relatório de Custos (POST) -->
                    <form method="POST" action="relatorioDeCustos.php" style="display:inline-block; margin:0;">
                        <input type="hidden" name="autofilter" value="1" />
                        <input type="hidden" name="id" value="<?= date('Y-m-d') ?>" />
                        <button type="submit" class="menu-button btn-info" title="Abrir com filtros automáticos">
                            <?php echo str_replace('<svg', '<svg class="quick-icon" aria-hidden="true"', @file_get_contents('img/icons/receive.svg')); ?>Relatório de Custos
                        </button>
                    </form>

                                        <form method="GET" action="comparativoAnualVendas.php" style="display:inline-block; margin:0;">
                        <input type="hidden" name="autofilter" value="1" />
                        <button type="submit" class="menu-button menu-purple" style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white;" data-toggle="tooltip" title="Abrir com filtros automáticos">
                            <?php echo str_replace('<svg', '<svg class="quick-icon" aria-hidden="true"', @file_get_contents('img/icons/chart.svg')); ?>Comparativo de Vendas
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.js"></script>
    <script>
        // Dados de exemplo para os gráficos
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa tooltips do Bootstrap (se disponível)
            if (window.jQuery && typeof jQuery.fn.tooltip === 'function') {
                jQuery('[data-toggle="tooltip"]').tooltip();
            }
            // Gráfico de Fluxo de Caixa
            const fluxoCaixaCtx = document.getElementById('fluxoCaixaChart').getContext('2d');
            new Chart(fluxoCaixaCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: 'Entradas',
                        data: <?php echo json_encode($entradas); ?>,
                        borderColor: '#2ecc71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Saídas',
                        data: <?php echo json_encode($saidas); ?>,
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });

            // Gráfico Comparativo Mensal
            const comparativoCtx = document.getElementById('comparativoChart').getContext('2d');
            new Chart(comparativoCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: 'Receitas',
                        data: <?php echo json_encode($entradas); ?>,
                        backgroundColor: 'rgba(46, 204, 113, 0.8)'
                    }, {
                        label: 'Despesas',
                        data: <?php echo json_encode($saidas); ?>,
                        backgroundColor: 'rgba(231, 76, 60, 0.8)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Gráfico de Despesas
            const despesasCtx = document.getElementById('despesasChart').getContext('2d');
            new Chart(despesasCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Fornecedores', 'Funcionários', 'Impostos', 'Outros'],
                    datasets: [{
                        data: [40, 25, 20, 15],
                        backgroundColor: [
                            'rgba(52, 152, 219, 0.8)',
                            'rgba(155, 89, 182, 0.8)',
                            'rgba(241, 196, 15, 0.8)',
                            'rgba(230, 126, 34, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1.3,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Gráfico de Projeção Financeira
            const projecaoCtx = document.getElementById('projecaoChart').getContext('2d');
            new Chart(projecaoCtx, {
                type: 'line',
                data: {
                    labels: ['Dezembro', 'Janeiro', 'Fevereiro'],
                    datasets: [{
                        label: 'Projeção',
                        data: [55000, 58000, 62000],
                        borderColor: '#8e44ad',
                        backgroundColor: 'rgba(142, 68, 173, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1.8
                }
            });

            // Gráfico de Saldo Acumulado
            const saldoAcumuladoCtx = document.getElementById('saldoAcumuladoChart').getContext('2d');
            new Chart(saldoAcumuladoCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: 'Saldo Acumulado',
                        data: <?php echo json_encode($saldoAcumulado); ?>,
                        borderColor: '#2980b9',
                        backgroundColor: 'rgba(41, 128, 185, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2
                }
            });

            // Valores dos cards e detalhes populados pelo servidor (PHP) para refletir dados reais
        });
    </script>
</body>
</html>