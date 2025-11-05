<?php
$inicio = $_SESSION['inicial'];
$fim = adicionaDias($inicio, 4); // de segunda à sexta
$produtor = $_SESSION['opprod'];
?>
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<div style="display: inline-block; vertical-align: top; border: #000 1px solid; margin: 0 10px">
    <table id="listagem" cellspacing="1" class="table-bordered table-condensed table-striped table-hover" style="margin: 0 0 10px; width: 100%">
        <thead>
        <tr>
            <th colspan="4" class="text-center resumo" style="font-weight: bold">RESUMO DA SEMANA</th>
        </tr>
        <tr>
            <th colspan="4" class="text-center"><?php
                $dti = new DateTime($inicio);
                echo $dti->format('d/m/Y');
                $dtf = new DateTime($fim);
                echo " a ";
                echo $dtf->format('d/m/Y');
                ?><br>
                Semana: <?php echo $dti->format('W'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $cxafit_total_debito = 0;
        $cxafit_total_credito = 0;
        $dados_rec['TOTAL'] = NULL;
        $dados_pag['TOTAL'] = NULL;
        //$tmp_saldo_cxafit = 0;

        $estven = new estven($mysql);
        $scccad = new scccad($mysql);
        $sccdia = new sccdia($mysql);
        $sccpla = new sccpla($mysql);
        $cxafit = new cxafit($mysql);
        $sccmovclass = new sccmov($mysql);
        $sccmov = array();
        $find = 0;
        $imp_saldo = 0;
        ?>

        <?php
        if (isset($valores['tipo']) and $valores['tipo'] == 'cxafit') {
            if ($valores["FI_VAL"] > 0) {
                $color = 'blue';
            } elseif ($valores["FI_VAL"] < 0) {
                $color = 'red';
            } else {
                $color = NULL;
            }
            ?>


            <?php
            if ($valores['FI_LIN'] == 0) {
                $tmp_saldo_cxafit = number_format($valores["FI_VAL"], 2, ',', '.');
            }

        } elseif (isset($valores['tipo']) and $valores['tipo'] == "sccmov") {
            $sccmov[] = $valores;
            if ($valores['MV_VLV'] <= 0) {
                $dados_pag['TOTAL'] += $valores['MV_VLV'];
            } elseif ($valores['MV_VLV'] > 0) {
                $dados_rec['TOTAL'] += $valores['MV_VLV'];
            }
        }
        $cxafit_ultimadata = !empty($valores['FI_DTA']) ? $valores['FI_DTA'] : null;
        ?>
        </tbody>
    </table>
    <table id="listagem2" cellspacing="1" class="table-bordered table-condensed table-striped table-hover" style="margin: 0 0 10px;  width: 100%">

        <tbody>

        <?php
        if ($_POST['tipo'] == 1) {
            //Select SUM Recebimento
            $dados_rec = $mysql->sum("sccmov", "`MV_VLV`", "`MV_VCT` >= '$inicio' AND `MV_VCT` <= '$fim' AND `MV_CDES` <> 1 AND `MV_PGT` = 0 AND `MV_TIP` >= 5 AND `MV_TIP` <= 9 ORDER BY `MV_VCT`");
            //Select SUM Pagamento
            $dados_pag = $mysql->sum("sccmov", "`MV_VLV`", "`MV_VCT` >= '$inicio' AND `MV_VCT` <= '$fim' AND `MV_PGT` = 0 AND `MV_TIP` >= 0 AND `MV_TIP` <= 4 AND `MV_CTA` <> '$produtor' ORDER BY `MV_VCT`");
            //Select SUM Pagamento Produtores
            $dados_pagprod = $mysql->sum("sccmov", "`MV_VLV`", "`MV_VCT` >= '$inicio' AND `MV_VCT` <= '$fim' AND `MV_PGT` = 0 AND `MV_TIP` >= 0 AND `MV_TIP` <= 4 AND `MV_CTA` = '$produtor' ORDER BY `MV_VCT`");
            ?>

            <tr>
                <!-- PROVISÕES RECEBIMENTO -->
                <th colspan="2" class="text-right">Provisões Recebimento:</th>
                <th style="color: blue; " class="text-right">R$ <?php echo number_format(isset($dados_rec["TOTAL"]) ? $dados_rec["TOTAL"] : 0, 2, ',', '.') ?></th>
                <th></th>
            </tr>
            <tr>
                <!-- PROVISÕES PAGAMENTO -->
                <th colspan="2" class="text-right" style="">Provisões Pagamento:</th>
                <th></th>
                <th style="color: red;" class="text-right">R$ <?php echo number_format(isset($dados_pag["TOTAL"]) ? $dados_pag["TOTAL"] : 0, 2, ',', '.') ?></th>
            </tr>
            <tr>
                <!-- PROVISÕES PAGAMENTO PRODUTORES -->
                <th colspan="2" class="text-right" style="">Provisões Pagamento Produtores:</th>
                <th></th>
                <th style="color: red;" class="text-right">R$ <?php echo number_format(isset($dados_pagprod['TOTAL']) ? $dados_pagprod['TOTAL'] : 0, 2, ',', '.') ?></th>
            </tr>
            <?php
        } elseif ($_POST['tipo'] == 2) {
            ?>
            <?php
            $total_saldo_provisionado = 0;
            sort($sccmov);
            foreach ($sccmov as $key => $valores) {
                if ($valores["MV_VLV"] > 0) {
                    $color = 'yelow';
                } elseif ($valores["MV_VLV"] < 0) {
                    $color = 'red';
                } else {
                    $color = NULL;
                }
                ?>
                <tr>
                    <th style="color: black; background: <?= $dta_mod = $valores['dta_mod'] == 's' ? '#FFFF66' : null ?>"><?= $valores['MV_NUM'] ?></th>
                    <th style="color: black; background: <?= $dta_mod = $valores['dta_mod'] == 's' ? '#FFFF66' : null ?>"><?= $v = $valores["MV_REP"] <> 0 ? $scccad->getCad($valores["MV_CAD"]) . " (" . $estven->getRep($valores['MV_REP']) . ")" : $scccad->getCad($valores["MV_CAD"]) ?></th>
                    <th style="color: blue; background: <?= $dta_mod = $valores['dta_mod'] == 's' ? '#FFFF66' : null ?>" class="text-right"><?= $v = $valores["MV_VLV"] > 0 ? "R$ " . number_format($valores["MV_VLV"], 2, ',', '.') : "" ?></th>
                    <th style="color: red; background: <?= $dta_mod = $valores['dta_mod'] == 's' ? '#FFFF66' : null ?>" class="text-right"><?= $v = $valores["MV_VLV"] <= 0 ? "R$ " . number_format($valores["MV_VLV"], 2, ',', '.') : "" ?></th>
                </tr>


                <?php
            }
        }
        $totPag = 0;
        if (isset($dados_pag['TOTAL']) and isset($dados_pagprod['TOTAL'])) {
            $totPag = $dados_pag['TOTAL'] + $dados_pagprod['TOTAL'];
        }

        ?>
        <tr>
            <th class="text-right" style="font-weight: bold" colspan="2">Total Provisionado:</th>
            <!-- TOTAL PROVISIONADO DÉBITO -->
            <th style="font-weight: bold; color: blue" class="text-right"><?= "R$ " . number_format(isset($dados_rec["TOTAL"]) ? $dados_rec["TOTAL"] : 0, 2, ',', '.') ?></th>
            <!-- TOTAL PROVISIONADO CRÉDITO -->
            <th style="font-weight: bold; color: red" class="text-right"><?= "R$ " . number_format($totPag, 2, ',', '.') ?></th>
        </tr>
        <tr>
            <th class="text-right" style="font-weight: bold" colspan="2">Saldo Provisionado:</th>

            <?php
            $sccmov_total = $totPag + $dados_rec["TOTAL"];
            ?>
            <!-- SALDO PROVISIONADO DÉBITO -->
            <th style="font-weight: bold; color: blue" class="text-right"><?= $totaldeb = $sccmov_total >= 0 ? "R$ " . number_format($sccmov_total, 2, ',', '.') : NULL ?></th>
            <!-- SALDO PROVISIONADO CRÉDITO -->
            <th style="font-weight: bold; color: red" class="text-right"><?= $totalcre = $sccmov_total < 0 ? "R$ " . number_format($sccmov_total, 2, ',', '.') : NULL ?></th>
        </tr>
        </tbody>
        <thead>
        <tr>
            <th colspan="4" class="text-center">PROVISÕES</th>
        </tr>
        <?php
        if ($_POST['tipo'] == 1) { ?>
            <th colspan="2"></th>
            <th style="font-weight: bold">Débito</th>
            <th style="font-weight: bold">Crédito</th>
            <?php
        } elseif ($_POST['tipo'] == 2) { ?>
            <tr>
                <th style="font-weight: bold">Duplicata</th>
                <th style="font-weight: bold">Nome (Representante)</th>
                <th style="font-weight: bold">Débito</th>
                <th style="font-weight: bold">Crédito</th>
            </tr>
        <?php } ?>
        </thead>
    </table>
    <?php
    ?>
</div>