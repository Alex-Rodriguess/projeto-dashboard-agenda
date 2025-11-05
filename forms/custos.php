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
<form id="form1" name="form1" class="form" role="form" action="relatorioDeCustos.php" method="post">
    <div class="row">
        <div class="col-md-3 col-md-offset-0 data">
            <div class="titlebar">Data de Vencimento Inicial</div>
            <input id="dtaIn" name="dtaVenIn" type="text" value="<?= isset($_POST['dtaIn']) ? $_POST['dtaIn'] : null ?>" class="form-control" placeholder="Data inicial"/>

            <div class="titlebar">Data de Vencimento Final</div>
            <input id="dtaFi" name="dtaVenFi" type="text" value="<?= isset($_POST['dtaFi']) ? $_POST['dtaFi'] : null ?>" class="form-control" placeholder="Data Final"/>
        </div>
        <div class="col-md-3 col-md-offset-0">
            <div class="titlebar">Vendas no mês</div>
            <input id="venda" name="venda" type="text" onkeypress="return SomenteNumero(event)" class="form-control" value="50000" required/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">

            <input id="submit" name="submit" type="submit" value="Filtrar" class="btn btn-info"/>
            <input id="reset" name="reset" type="button" value="Limpar Filtro" class="btn btn-default"/>

        </div>
    </div>
</form>