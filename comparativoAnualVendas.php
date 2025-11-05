<?php
include ("./config.php");
//Se as variaveis de login e senha não estão setadas e estão diferentes das
// variaveis, redireciona pra tela de login
if ((!isset($_SESSION["log"]) or $_SESSION["log"] <> $user) and (!isset($_SESSION["senha"]) or $_SESSION["senha"] <> $senha)) {
	header("Location: login.php");
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Comparativo anual de vendas</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="css/cupertino/jquery-ui.min.css">
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
		<script src="js/jquery-1.10.2.js"></script>
		<script src="js/scripts.js"></script>
		<script src="js/jquery-ui-1.10.4.custom.js"></script>
		<script src="js/i18n/jquery.ui.datepicker-pt-BR.js"></script>
		<script src="js/jquery.maskedinput.min.js"></script>
		<script src="js/bootstrap.js"></script>
		<!-- <script src="js/highslide-full.min.js"></script> -->
		
		<!-- <link rel="stylesheet" type="text/css" href="css/highslide.css" /> -->
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<script type="text/javascript">
			// hs.graphicsDir = 'css/graphics/';
			// hs.outlineType = 'rounded-white';
			// hs.wrapperClassName = 'draggable-header';
			// hs.width = "1000";
			// hs.dimmingOpacity = 0.8;

			$(function() {
				$("#datainicial, #datafinal").datepicker({
					dateFormat : 'dd/mm/yy',
					showButtonPanel : true
				});
				$("#datainicial, #datafinal").mask("99/99/9999");
			    
			});
		</script>
	</head>
	<body style="white-space: nowrap;">
		<div id="dvLoading"><img src="img/loading2.gif"></div>
		<?php
		include ("menu.php");
		?>
		<!-- style="padding:10px 0 0; background: #f7f7f7;" -->
		<div class="container">
			<div class="row">
				<div class="col-sm-12 panel panel-default">
					<?php
					include ('forms/comparativos.php');
					?>
				</div>
			</div>
		</div>
		<div class="container-fluid" style="margin: 0 20px 0">
			<div class="row">
				<?php
				if ($rs_list == NULL) {
					echo '<h3>SELECIONE UM BANCO E UMA DATA</h3>';
				} else {
					if ($row_tmp <= 0) {
						echo '<h3>Nenhum dado encontrado no período selecionado.</h3>';
					} else {
						if ($row_tmp > 0) {
							$proximo_saldo = 0;
							$count = 0;
							foreach ($dias as $dia => $dadosProntos) {
								include ('consultaBancoTabela.php');
								$count++;
							}

						}
					}
				}
				?>
			</div>
		</div>
	</body>
</html>
