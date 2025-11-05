<div style="display: inline-block; vertical-align: top; border: #000 1px solid; margin: 0 10px">
    <table id="listagem" cellspacing="1" class="table-bordered table-condensed table-striped table-hover" style="margin: 0 0 10px; width: 100%">
        <thead>
        <tr>
            <th colspan="4" class="text-center"><?php
                $date = new DateTime($dia);
                echo MyDateTime::format($date->format('d/m/Y'), 'd/m/Y', 'd/m/Y - l')
                ?></th>
        </tr>
        <tr>
            <th>Data</th>
            <th>Descrição</th>
            <th>Débito</th>
            <th>Crédito</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $cxafit_total_debito = 0;
        $cxafit_total_credito = 0;
        $dados_rec["TOTAL"] = 0;
        $dados_pag["TOTAL"] = 0;
        $dados_pagprod = 0;
        //$tmp_saldo_cxafit = 0;

        $estven = new estven($mysql);
        $scccad = new scccad($mysql);
        $sccdia = new sccdia($mysql);
        $sccpla = new sccpla($mysql);
        $cxafit = new cxafit($mysql);
        $sccmovclass = new sccmov($mysql);
        $sccmov = array();
        $cxaprev = array();
        $find = 0;
        $imp_saldo = 0;
        foreach ($dadosProntos as $key => $valores) {
            if ($valores['dta_mod'] == 'w' and $find == 0) {
                $find = 1;
                ?>
                <tr>
                    <th colspan="4" class="text-center find" style="font-weight: bold">SÁBADO/DOMINGO</th>
                </tr>
                <?php
            }

            switch ($valores['tipo']) {
                case "cxafit";
                    if ($valores["FI_VAL"] > 0) {
                        $color = 'blue';
                    } elseif ($valores["FI_VAL"] < 0) {
                        $color = 'red';
                    } else {
                        $color = NULL;
                    }
                    ?>
                    <tr>
                        <th><?php echo $data = date("d/m/Y", strtotime($valores["FI_DTA"])); ?></th>
                        <th style="color: black"><?php
                            $valores["FI_TDC"] = utf8_encode($valores["FI_TDC"]);
                            if ($valores["FI_ADT"] == 1) {
                                ?>
                                <a href="consultaTitulos.php?id=<?php echo $valores["FI_DTA"] ?>" onclick="return hs.htmlExpand(this, {objectType: 'iframe'})"><?php echo $valores["FI_TDC"] ?></a>
                                <?php
                            } elseif ($valores["FI_LIN"] == 0) {
                                echo "Saldo anterior... ";
                                //var_dump($valores);
                            } elseif (preg_match('/^Duplicata Nº/', $valores["FI_TDC"])) {
                                $sccdia_cad = $sccdia->getLan($valores["FI_LAN"]);
                                $cliente = $scccad->getCad($sccdia_cad);
                                // echo '<a href="#" class="nolink" title="'.$cliente.' - '.$sccmovclass->getNBC($sccdia->getFat($valores['FI_LAN'])).'" style="color: '.$color.'">'.$valores["FI_TDC"]."</a>";
                                echo $cliente . ' - ' . $valores["FI_TDC"];
                            } else {
                                echo $valores["FI_TDC"];
                            }
                            ?>
                        </th>
                        <th style="color:blue;" class="text-right"><?php
                            if ($valores["FI_VAL"] > 0) {
                                $val = "R$ " . number_format($valores["FI_VAL"], 2, ',', '.');
                                $cxafit_total_debito += $valores["FI_VAL"];
                            } elseif ($valores["FI_VAL"] >= 0 AND $valores["FI_VDD"] == 0 AND $valores['FI_LIN'] == 0) {
                                $val = "R$ " . number_format($valores["FI_VAL"], 2, ',', '.');
                            } else {
                                $val = NULL;
                            }
                            echo $val;
                            ?></th>
                        <th style="color:red" class="text-right"><?php
                            if ($valores["FI_VDD"] > 0) {
                                $valores['FI_VDD'] = $valores['FI_VDD'] * -1;
                                $val = "R$ " . number_format($valores['FI_VDD'], 2, ',', '.');
                                $cxafit_total_credito -= $valores["FI_VDD"];
                            } elseif ($valores["FI_VAL"] <= 0 AND $valores['FI_LIN'] == 0) {
                                $val = "R$ " . number_format($valores["FI_VAL"], 2, ',', '.');
                                $cxafit_total_credito -= $valores["FI_VAL"];
                            } else {
                                $val = null;
                            }
                            ////$valores['FI_VAL'] = $valores['FI_VAL'] * -1;
                            //                    $val = "R$ " . number_format($valores['FI_VAL'], 2, ',', '.');
                            //                    $cxafit_total_credito -= $valores["FI_VAL"];
                            echo $val;
                            ?></th>
                    </tr>

                    <?php
                    if ($valores['FI_LIN'] == 0) {
                        $tmp_saldo_cxafit = number_format($valores["FI_VAL"], 2, ',', '.');
                    }
                    break;

                case "sccmov":

                    $sccmov[] = $valores;
                    if ($valores['MV_TIP'] < 5) {
                        if ($valores['MV_CTA'] == $_SESSION['opprod']) {
                            $dados_pagprod += (abs($valores['MV_VLV']) * -1);
                        } else {
                            $dados_pag['TOTAL'] += (abs($valores['MV_VLV']) * -1);
                        }
                    } elseif (($valores['MV_TIP'] >= 5) && ($valores['MV_CDES'] != 1)) {
                        $dados_rec["TOTAL"] += abs($valores['MV_VLV']);
                    }
                    break;

                case "cxaprev":
                    $cxaprev[] = $valores;
                    $dados_pag["TOTAL"] += (abs($valores['PRE_VLR']) * -1);
                    break;

                default:
                    break;
            }

            $cxafit_ultimadata = !empty($valores['FI_DTA']) ? $valores['FI_DTA'] : null;
        } ?>
        <tr>
            <th colspan="2" class="text-right" style="font-weight: bold">Total:</th>
            <th style="color:blue; font-weight: bold" class="text-right">R$ <?= number_format($cxafit_total_debito, 2, ',', '.'); ?></th>
            <th style="color: red; font-weight: bold" class="text-right">R$ <?php $cxafit_total_credito = $cxafit_total_credito * -1;
                echo number_format($cxafit_total_credito, 2, ',', '.'); ?></th>
        </tr>
        <?php
        $cxafit_saldo_debito = NULL;
        $cxafit_saldo_credito = NULL;
        $cxafit_saldo = $cxafit_total_debito + $cxafit_total_credito;
        $ultimo_saldo = $cxafit_saldo;
        if ($cxafit_saldo >= 0) {
            $cxafit_saldo_debito = "R$ " . number_format($cxafit_saldo, 2, ',', '.');
        } elseif ($cxafit_saldo < 0) {
            $cxafit_saldo_credito = "R$ " . number_format($cxafit_saldo, 2, ',', '.');
        }

        ?>
        <tr>
            <th colspan="2" class="text-right" style="font-weight: bold">Saldo:</th>
            <th style="color:blue; font-weight: bold" class="text-right"><?php echo $cxafit_saldo_debito ?></th>
            <th style="color: red; font-weight: bold" class="text-right"><?php echo $cxafit_saldo_credito ?></th>
        </tr>
        <?php
        //        }
        ?>
        </tbody>
    </table>
    <table id="listagem2" cellspacing="1" class="table-bordered table-condensed table-striped table-hover" style="margin: 0 0 10px;  width: 100%">
        <tbody>
        <?php
        $sccmov_saldo_anterior_deb = null;
        $sccmov_saldo_anterior_cre = null;

        $sqlTitDescPag = "SELECT SUM(ABS(M.MV_VLV)) AS SOMA
	                             FROM SCCMOV AS M
	                             INNER JOIN SCCDIA AS D
	                             	ON M.MV_LAN = D.DI_FAT 
	                               AND M.MV_PGT = D.DI_DTA
	                             INNER JOIN SCCPLA AS P
	                             	ON D.DI_CTA = P.PL_COD
							     WHERE M.MV_BCO = 3 AND 
								       M.MV_CDES = 1 AND 
	                                   M.MV_PGT = $dia;";
        $TitDescPag = $mysql->query($sqlTitDescPag);
        $TitDescPagR = mysqli_result($TitDescPag, 0, 'SOMA');

        $sqlTitVenc = "SELECT SUM(ABS(M.MV_VLV)) AS SOMA
	                             FROM SCCMOV AS M
	   					           WHERE 
                                       M.MV_CDES = 1 AND 
									   M.MV_PGT = 0 AND
	                                   M.MV_VCT = $dia;";

        $_SESSION["sqlTitVenc"] = $sqlTitVenc;
        $TitVenc = $mysql->query($sqlTitVenc);
        $TitVencR = mysqli_result($TitVenc, 0, 'SOMA');

        $rs_total_provisionado = $mysql->query("SELECT SUM(`MV_VLV`) as total FROM SCCMOV WHERE `MV_PGT` = 0 AND `MV_VCT` < " . $dia);
        $total_provisionado_anterior = mysqli_result($rs_total_provisionado, 0, 'total');
        if ($total_provisionado_anterior >= 0) {
            $sccmov_saldo_anterior_deb = "R$ " . number_format($total_provisionado_anterior, 2, ',', '.');
        } elseif ($total_provisionado_anterior < 0) {
            $sccmov_saldo_anterior_cre = "R$ " . number_format($total_provisionado_anterior, 2, ',', '.');
        }

        if ($_POST['tipo'] == 1) { ?>
            <!-- SALDO PROVISIONADO ANTERIOR -->
            <tr>
                <td class="text-right" colspan="2">Saldo provisionado anterior...</td>
                <td style="color: blue" class="text-right"><?= $sccmov_saldo_anterior_deb ?></td>
                <td style="color: red" class="text-right"><?= $sccmov_saldo_anterior_cre ?></td>
            </tr>

            <!-- PROVISÕES RECEBIMENTO -->
            <tr>
                <td colspan="2" class="text-right"><a href="provisoesRecebimento.php?id=<?php echo $dia ?>" onclick="return hs.htmlExpand(this, {objectType: 'iframe'})">Provisões Recebimento</a></td>
                <td style="color: blue; " class="text-right">R$ <?php echo number_format($dados_rec["TOTAL"], 2, ',', '.') ?></td>
                <td></td>
            </tr>

            <!-- PROVISÕES RECEBIMENTO DESCONTADOS -->
            <tr>
                <td colspan="2" class="text-right" style=""><a href="provisoesRecebimentoDescontados.php?tipo=prod&id=<?php echo $dia ?>" onclick="return hs.htmlExpand(this, {objectType: 'iframe'})">Provisões Recebimento Descontados</a></td>
                <td style="color: blue;" class="text-right">R$ <?php echo number_format($TitVencR, 2, ',', '.') ?></td>
            </tr>

            <!-- PROVISÕES PAGAMENTO NORMAL -->
            <tr>
                <td colspan="2" class="text-right" style=""><a href="provisoesPagamento.php?id=<?php echo $dia ?>" onclick="return hs.htmlExpand(this, {objectType: 'iframe', width: '1100'})">Provisões Pagamento</a></td>
                <td></td>
                <td style="color: red;" class="text-right">R$ <?php echo number_format($dados_pag["TOTAL"], 2, ',', '.') ?></td>
            </tr>

            <!-- PROVISÕES PAGAMENTO PRODUTORES -->
            <tr>
                <td colspan="2" class="text-right" style=""><a href="provisoesPagamento.php?tipo=prod&id=<?php echo $dia ?>" onclick="return hs.htmlExpand(this, {objectType: 'iframe'})">Provisões Pagamento Produtores</a></td>
                <td></td>
                <td style="color: red;" class="text-right">R$ <?php echo number_format($dados_pagprod, 2, ',', '.') ?></td>
            </tr>
            <!-- TÍTULOS DESCONTADOS PAGOS -->
            <tr>
                <td colspan="2" class="text-right" style=""><a href="titulosDescontadosPagos.php?tipo=prod&id=<?php echo $dia ?>" onclick="return hs.htmlExpand(this, {objectType: 'iframe'})">Títulos Descontados Pagos</a></td>
                <td style="color: blue;" class="text-right">R$ <?php echo number_format($TitDescPagR, 2, ',', '.') ?></td>
            </tr>
            <?php
        } elseif ($_POST['tipo'] == 2) {
            $total_saldo_provisionado = 0;

            //sort($cxaprev);
            foreach ($cxaprev as $key => $valores) {
                echo "<tr style='background-color: yellow;'>";
                echo "<td style='background-color: yellow;'></td>";
                echo "<td style='background-color: yellow;'>" . $valores["PRE_CAD"] . "</td>";
                echo "<td style='background-color: yellow;'></td>";
                echo "<td style='background-color: yellow;' class='text-right'>R$ " . number_format(($valores["PRE_VLR"] * -1), 2, ',', '.') . "</td>";
                echo "</tr>";
            }

            sort($sccmov);
            $grupFatura = [];

            foreach ($sccmov as $key => $valores) {
                if ($sccpla->getPLCCAS($valores['MV_CTA']) == 1 and $valores['MV_FAT'] > 0) {
                    $ano = substr($valores['MV_VCT'], 2, 2);
                    if (isset($grupFatura[$valores['MV_FAT'] . '/' . $ano][$valores['MV_CAD']])) {
                        $grupFatura[$valores['MV_FAT'] . '/' . $ano][$valores['MV_CAD']] += $valores['MV_VLV'];
                    } else {
                        $grupFatura[$valores['MV_FAT'] . '/' . $ano][$valores['MV_CAD']] = $valores['MV_VLV'];
                    }
                }
            }

            foreach ($grupFatura as $key => $value) {
                ?>
                <tr>
                    <td style="color: black;"><?= $key ?></td>
                    <td style="color: black; font-weight: bold"><?= $scccad->getCad(key($value)) ?></td>
                    <td style="color: blue;" class="text-right"><?= $value[key($value)] > 0 ? "R$ " . number_format($value[key($value)], 2, ',', '.') : "" ?></td>
                    <td style="color: red;" class="text-right"><?= $value[key($value)] <= 0 ? "R$ " . number_format($value[key($value)], 2, ',', '.') : "" ?></td>
                </tr>
                <?php
            }
            foreach ($sccmov as $key => $valores) {
//                    $grupFatura[$]
                if ($sccpla->getPLCCAS($valores['MV_CTA']) == 1 and $valores['MV_FAT'] > 0) {
                    $ano = substr($valores['MV_VCT'], 2, 2);
                    if (isset($grupFatura[$valores['MV_FAT'] . '/' . $ano][$valores['MV_CAD']])) {
                        $grupFatura[$valores['MV_FAT'] . '/' . $ano][$valores['MV_CAD']] += $valores['MV_VLV'];
                    } else {
                        $grupFatura[$valores['MV_FAT'] . '/' . $ano][$valores['MV_CAD']] = $valores['MV_VLV'];
                    }
                } else {
                    if ($valores["MV_TIP"] > 4) {
                        $color = 'blue';
                    } elseif ($valores["MV_TIP"] < 5) {
                        $color = 'red';
                    } else {
                        $color = NULL;
                    }
                    ?>
                    <tr>
                        <td style="color: black; background: <?= $dta_mod = $valores['dta_mod'] == 's' ? '#FFFF66' : null ?>"><?= $valores['MV_NUM'] ?></td>
                        <td style="color: black; background: <?= $dta_mod = $valores['dta_mod'] == 's' ? '#FFFF66' : null ?>"><?= $v = $valores["MV_REP"] <> 0 ? $scccad->getCad($valores["MV_CAD"]) . " (" . $estven->getRep($valores['MV_REP']) . ")" : $scccad->getCad($valores["MV_CAD"]) ?></td>
                        <td style="color: blue; background: <?= $dta_mod = $valores['dta_mod'] == 's' ? '#FFFF66' : null ?>" class="text-right"><?= $v = $valores["MV_VLV"] > 0 ? "R$ " . number_format($valores["MV_VLV"], 2, ',', '.') : "" ?></td>
                        <td style="color: red; background: <?= $dta_mod = $valores['dta_mod'] == 's' ? '#FFFF66' : null ?>" class="text-right"><?= $v = $valores["MV_VLV"] <= 0 ? "R$ " . number_format($valores["MV_VLV"], 2, ',', '.') : "" ?></td>
                    </tr>

                    <?php
                }
            }


        }
        $totalprov_pag = $dados_pagprod + $dados_pag['TOTAL'];
        ?>
        <tr>
            <th class="text-right" style="font-weight: bold" colspan="2">Total Provisionado:</th>
            <th style="font-weight: bold; color: blue" class="text-right"><?= "R$ " . number_format($dados_rec["TOTAL"], 2, ',', '.') ?></th>
            <th style="font-weight: bold; color: red" class="text-right"><?= "R$ " . number_format($totalprov_pag, 2, ',', '.') ?></th>
        </tr>
        <tr>
            <?php
            $sccmov_total = $dados_pag["TOTAL"] + $dados_rec["TOTAL"] + $dados_pagprod;
            ?>
            <th class="text-right" style="font-weight: bold" colspan="2">Saldo Provisionado:</th>
            <th style="font-weight: bold; color: blue" class="text-right"><?= $totaldeb = $sccmov_total >= 0 ? "R$ " . number_format($sccmov_total, 2, ',', '.') : NULL ?></th>
            <th style="font-weight: bold; color: red" class="text-right"><?= $totalcre = $sccmov_total < 0 ? "R$ " . number_format($sccmov_total, 2, ',', '.') : NULL ?></th>
        </tr>
        </tbody>
        <thead>
        <tr>
            <th colspan="4" class="text-center">PROVISÕES</th>
        </tr>
        <tr>
            <th colspan="2" class="text-right">Soma:</th>
            <th colspan="2" class="text-right"><input type="text" name="somaHead" class="text-right soma" readonly></th>
        </tr>
        <?php
        if ($_POST['tipo'] == 1) { ?>
            <tr>
                <th colspan="2"></th>
                <th style="font-weight: bold">Débito</th>
                <th style="font-weight: bold">Crédito</th>
            </tr>
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
        <tfoot>
        <tr>
            <th colspan="2" class="text-right">Soma:</th>
            <th colspan="2" class="text-right"><input type="text" name="somaFoot" class="text-right soma" readonly></th>
        </tr>
        </tfoot>
    </table>
</div>