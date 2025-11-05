<form id="form1" name="form1" class="form" role="form" action="consultaDuplicata.php" method="post">
    <div class="row">
        <div class="col-md-2 col-md-offset-1 data">
            <div class="titlebar">Data de Vencimento</div>
            <input id="dtaVenIn" name="dtaVenIn" type="text" value="<?php echo isset($_POST['dtaVenIn']) ? $_POST['dtaVenIn'] : null ?>" class="form-control" placeholder="Data inicial"/>
            <input id="dtaVenFi" name="dtaVenFi" type="text" value="<?php echo isset($_POST['dtaVenFi']) ? $_POST['dtaVenFi'] : null ?>" class="form-control" placeholder="Data Final"/>
        </div>
        <div class="col-md-2 data">
            <div class="titlebar">Data de Emissão</div>
            <input id="dtaEmiIn" name="dtaEmiIn" type="text" value="<?php echo isset($_POST['dtaEmiIn']) ? $_POST['dtaEmiIn'] : null ?>" class="form-control" placeholder="Data inicial"/>
            <input id="dtaEmiFi" name="dtaEmiFi" type="text" value="<?php echo isset($_POST['dtaEmiFi']) ? $_POST['dtaEmiFi'] : null ?>" class="form-control" placeholder="Data Final"/>
        </div>
        <!-- <div class="col-md-2 data">
			<div class="titlebar">Data de Pagamento</div>
			<input id="dtaPagIn" name="dtaPagIn" type="text" value="<?php echo  isset($_POST['dtaPagIn']) ? $_POST['dtaPagIn'] : null ?>" class="form-control" placeholder="Data inicial" />
			<input id="dtaPagFi" name="dtaPagFi" type="text" value="<?php echo  isset($_POST['dtaPagFi']) ? $_POST['dtaPagFi'] : null ?>" class="form-control" placeholder="Data Final" />
		</div> -->
        <div class="col-md-2">
            <div class="titlebar">Receber ou Pagar</div>
            <?php
            $sel1 = null;
            $sel2 = null;
            $sel3 = null;
            if (isset($_POST['pagrec'])) {
                if ($_POST['pagrec'] == 1) {
                    $sel1 = ' checked="checked"';
                } elseif ($_POST['pagrec'] == 2) {
                    $sel2 = ' checked="checked"';
                } elseif ($_POST['pagrec'] == 0) {
                    $sel3 = ' checked="checked"';
                } else {
                    $sel3 = ' checked="checked"';
                }
            }
            ?>
            <div class="radio">
                <label><input id="arec" name="pagrec" value="1" type="radio"<?= $sel1 ?> /> Receber</label>
            </div>
            <div class="radio">
                <label><input id="apag" name="pagrec" value="2" type="radio"<?= $sel2 ?> /> Pagar</label>
            </div>
            <div class="radio">
                <label for="ambos"><input id="ambos" name="pagrec" value="0" type="radio"<?= $sel3 ?> />Ambos</label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="titlebar">Cliente Inicial</div>

            <select id="cliIn" name="cliIn" class="form-control">
                <option value="0">Listar todos</option>
                <?php
                foreach ($scccad->getListClient() as $key => $cad) {
                    if (isset($_POST['cliIn']) and $_POST['cliIn'] == $cad['SC_CAD']) {
                        $selCadIn = ' selected="selected"';
                    } else {
                        $selCadIn = null;
                    }
                    ?>
                    <option value="<?= $cad['SC_CAD'] ?>"<?= $selCadIn ?>><?= $cad['SC_CAD'] . " - " . utf8_encode($cad['SC_NOM']) ?></option>
                    <?php
                }
                ?>
            </select>
            <div class="titlebar">Cliente Final</div>
            <select id="cliFi" name="cliFi" class="form-control">
                <option value="0">Listar todos</option>
                <?php
                foreach ($scccad->getListClient() as $key => $cad) {
                    if (isset($_POST['cliFi']) and $_POST['cliFi'] == $cad['SC_CAD']) {
                        $selCadFi = ' selected="selected"';
                    } else {
                        $selCadFi = null;
                    }
                    ?>
                    <option value="<?= $cad['SC_CAD'] ?>"<?= $selCadFi ?>><?= $cad['SC_CAD'] . " - " . $cad['SC_NOM'] ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">

            <input id="submit" name="submit" type="submit" value="Filtrar" class="btn btn-info"/>
            <input id="reset" name="reset" type="button" value="Limpar FIltro" class="btn btn-default"/>

        </div>
    </div>
    </div>
</form>