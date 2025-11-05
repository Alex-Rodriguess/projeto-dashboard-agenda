<script language='JavaScript'>
    function SomenteNumero(e) {
        var tecla = (window.event) ? event.keyCode : e.which;
        if ((tecla > 47 && tecla < 58)) return true;
        else {
            if (tecla == 8 || tecla == 0) return true;
            else return false;
        }
    }
</script>
<?php
if (empty($_SESSION['opprod'])) {
    $_SESSION['opprod'] = 27;
}
?>
<form method="POST" name="form" action="consultaBanco.php" class="form">
    <div class="row">
        <div class="col-md-3">
            <label for="caixa" class="control-label">Selecione:</label>
            <select id="caixa" name="caixa" class="form-control" required>
                <option value="">Selecione</option>
                <?php
                $sel = NULL;
                $rs = $mysql->query("SELECT * FROM cxaemp ORDER BY `CX_DSC` ASC");
                while ($dados = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
                    if (isset($_POST['caixa']) and $dados['CX_COD'] == $_POST['caixa']) {
                        $sel = ' selected="selected"';
                    } else {
                        $sel = NULL;
                    }
                    ?>
                    <option value="<?php echo $dados["CX_COD"] ?>"<?php echo $sel ?>><?php echo $dados["CX_DSC"] ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="datainicial">Data Inicial:</label>
            <input id="datainicial" name="datainicial" class="form-control" type="text" placeholder="Data Inicial" value="<?php echo $d = isset($_POST['datainicial']) ? $_POST['datainicial'] : NULL ?>" required/>
        </div>
        <div class="col-md-3">
            <label for="datafinal">Data Final:</label>
            <input id="datafinal" name="datafinal" class="form-control" type="text" placeholder="Data Final" value="<?php echo $d = isset($_POST['datafinal']) ? $_POST['datafinal'] : NULL ?>"/>
        </div>
        <div class="col-md-3">
            <label for="tipo">Tipo:</label>
            <select id="tipo" name="tipo" class="form-control" required>
                <?php
                $selTipo1 = NULL;
                $selTipo2 = NULL;
                if (isset($_POST['tipo'])) {
                    if ($_POST["tipo"] == 1) {
                        $selTipo1 = ' selected="selected"';
                    } elseif ($_POST["tipo"] == 2) {
                        $selTipo2 = ' selected="selected"';
                    }
                }
                ?>
                <option value="2"<?php echo $selTipo2 ?>>Analítico</option>
                <option value="1"<?php echo $selTipo1 ?>>Sintético</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <label for="opprod" class="control-label">Operação Produtores:</label>
            <input id="opprod" name="opprod" type="text" onkeypress="return SomenteNumero(event)" class="form-control" value="<?= $_SESSION['opprod'] ?>" required/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            <input id="enviar" name="enviar" type="submit" value="Filtrar" class="btn btn-info"/>
        </div>
    </div>
</form>