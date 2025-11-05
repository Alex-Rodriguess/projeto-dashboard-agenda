<nav class="navbar navbar-default navbar-custom" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-navbar" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">Dashboard</a>
        </div>

        <div class="collapse navbar-collapse" id="main-navbar">
            <ul class="nav navbar-nav main-tabs">
                <?php
                $pages = array();
                $pages["consultaBanco.php"] = "Consulta Bancos";
                $pages["consultaDuplicata.php"] = "Consulta Duplicatas em Atraso";
                $pages["agendaPrevisoes.php"] = "Agendamento de Provisões";
                $pages["relatorioDeCustos.php"] = "Relatório de Custos";
                //$pages["comparativoAnualVendas.php"] = "Comparativo anual de Vendas";
                $menu_self = explode("/", $_SERVER['PHP_SELF']);
                $activePage = end($menu_self);

                // mapa de ícones locais por rota e cores por aba
                $icons = array(
                    "consultaBanco.php" => "img/icons/bank.svg",
                    "consultaDuplicata.php" => "img/icons/list.svg",
                    "agendaPrevisoes.php" => "img/icons/invoice.svg",
                    "relatorioDeCustos.php" => "img/icons/chart.svg",
                );

                $tabColorMap = array(
                    "consultaBanco.php" => 'tab-primary',
                    "consultaDuplicata.php" => 'tab-warning',
                    "agendaPrevisoes.php" => 'tab-info',
                    "relatorioDeCustos.php" => 'tab-success',
                );

                foreach ($pages as $url => $title) {
                    $isActive = ($url === $activePage);
                    // sempre aplicar a cor correspondente (dá consistência visual)
                    $colorClass = isset($tabColorMap[$url]) ? $tabColorMap[$url] : 'tab-neutral';
                    $btnClass = 'tab-btn ' . $colorClass;
                    $iconPath = isset($icons[$url]) ? $icons[$url] : '';
                    ?>
                    <li class="<?php echo $isActive ? 'active' : ''; ?>">
                        <a href="<?= $url; ?>" class="<?= $btnClass ?>">
                            <?php if ($iconPath):
                                $svg = @file_get_contents($iconPath);
                                if ($svg) {
                                    // adiciona a classe ao elemento <svg> e exibe inline para permitir colorização via CSS
                                    echo str_replace('<svg', '<svg class="quick-icon" aria-hidden="true"', $svg);
                                }
                            endif; ?>
                            <?= $title; ?>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
</nav>
