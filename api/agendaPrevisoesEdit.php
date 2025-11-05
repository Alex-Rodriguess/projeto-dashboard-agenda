<?php
include ("config.php");
//Se as variaveis de login e senha não estão setadas e estão diferentes das
// variaveis, redireciona pra tela de login
if ((!isset($_SESSION["log"]) or $_SESSION["log"] <> $user) and (!isset($_SESSION["senha"]) or $_SESSION["senha"] <> $senha)) {
	header("Location: login.php");
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
	<script src="js/jquery-ui-1.10.4.custom.min.js"></script>
	<script src="js/jquery.meio.mask.js"></script>
	<script src="js/i18n/jquery.ui.datepicker-pt-BR.js"></script>
	<script src="js/bootstrap.js"></script>
	<script src="js/highslide-full.min.js"></script>
		
	<link rel="stylesheet" type="text/css" href="css/highslide.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link href="img/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<script type="text/javascript">
		$(function() {
			$('#reset').click(function() {
				parent.window.hs.getExpander().close();
			});
			$("#valor").setMask('signed-decimal');
			
		});
	</script>
	<style type="text/css">
		#valor {
			z-index: 1;
		}
	</style>
</head>
<body>
	<?php
	include ("menu.php");
	?>
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				<?php
				if (isset($_POST['submit']) AND $_POST['submit'] == 'Salvar') {
					$data = $util -> formataData($_POST['data']);
					$valor = $util -> formataDinheiro($_POST['valor']);
					$cli = $_POST['cliente'];
					$dados = array(
						'PRE_DTA' => $data,
						'PRE_VLR' => $valor,
                        'PRE_CAD' => $cli
					);

                    if (!empty($_POST['representante'])){
                        $dados['PRE_REP'] = $_POST['representante'];
                    }else{
                        $dados['PRE_REP'] = 0;
                    }

					$mysql->update('cxaprev', $dados, $_POST['id'], '`PRE_COD`');
				?>
				<h3 class="text-center">Alterado com sucesso!</h3>
				<script type='text/javascript'>
					setTimeout(function () {
						try {
							window.location.href = 'agendaPrevisoes.php';
						} catch (e) {}
					}, 1000);     
				</script>
	
				<?php
				} else {
					$sql = "SELECT * FROM cxaprev WHERE `PRE_COD` = '".$_GET['id']."';";
					$rs = $mysql->query($sql);
					$dados = mysqli_fetch_array($rs, MYSQLI_ASSOC);
					?>
				<form id="form" name="form" class="form" role="form" action="agendaPrevisoesEdit.php" method="post">
					<input type="hidden" id="id" name="id" value="<?= $dados['PRE_COD'] ?>" />
					<div class="form-group">
						<label>Data:</label>
						<input type="date" id="data" name="data" class="form-control" value="<?= $dados['PRE_DTA'] ?>" />
					</div>
					<div class="form-group">
						<label for="reresentante">Representante:</label>
						<select id="reresentante" name="representante" class="form-control">
                            <?php $estven->EditRep($dados['PRE_REP']) ?>
                        </select>
					</div>
					<div class="form-group">
						<label>Cliente:</label>
                        <select id="cliente" name="cliente" class="form-control" required>
                            <?php $scccad -> EditCli($dados['PRE_CAD']) ?>
                        </select>
					</div>
					<div class="form-group">
						<label>Valor:</label>
						<div class="input-group">
						<span class="input-group-addon">R$</span>
							<input type="text" id="valor" name="valor" value="<?= number_format($dados['PRE_VLR'], 2, ',', '') ?>" class="form-control" />
						</div>
					</div>
					<div class="form-group text-center">
						<input type="submit" id="submit" name="submit" value="Salvar" class="btn btn-success btn-group" />
						<input type="reset" id="reset" onclick="window.location.href='agendaPrevisoes.php'" name="reset" value="Cancelar" class="btn btn-danger btn-group" />
					</div>
				</form>
		
	<?php
	}
	?>		</div>
		</div>
	</div>
</body>
</html>