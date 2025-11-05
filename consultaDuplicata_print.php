<?php
include ("config.php");
//Se as variaveis de login e senha não estão setadas e estão diferentes das
// variaveis, redireciona pra tela de login
if ((!isset($_SESSION["log"]) or $_SESSION["log"] <> $user) and (!isset($_SESSION["senha"]) or $_SESSION["senha"] <> $senha)) {
	header("Location: login.php");
}

$scccad = new scccad($mysql);
$estven = new estven($mysql);
	$sql = $_SESSION['sql'];
	unset($_SESSION['sql']);
	$rs = $mysql->query($sql);
	$num = mysqli_num_rows($rs);

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Consulta Duplicatas</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="css/cupertino/jquery-ui.min.css">
		<script src="js/jquery-1.10.2.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/highslide-full.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<script language="JavaScript" type="text/javascript">
			window.onload = function() {
				window.print();
			}
		</script>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<table class="table table-condensed table-bordered table-striped">
						<thead>
							<tr>
								<th>Nº Lançamento</th>
								<th>Data Emissão</th>
								<th>Data do Vcto</th>
								<th>Parcela</th>
								<th>Nome (Representante)</th>
								<th>Saldo</th>
							</tr>
						</thead>
						<tbody>
					<?php
						$total = null;
						if ($num == 0) {
							echo '<tr><th colspan="6" class="text-center">Nenhum dado encontrado.</th</tr>';
						} else {
							while($dados = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
								?>
								<tr>
									<th><?= $dados['MV_LAN'] ?></th>
									<th><?php $date = new DateTime($dados['MV_DTE']); echo $date->format('d/m/Y'); ?></th>
									<th><?php $date = new DateTime($dados["MV_VCT"]); echo $date->format('d/m/Y'); ?></th>
									<th><?= $dados['MV_PAR'] ?></th>
									<th><?= $dados['MV_CAD'] ." - ". $scccad->getCad($dados['MV_CAD']); echo $rep = $dados['MV_REP'] <> 0 ? " (".$estven->getRep($dados['MV_REP']).")" : null; ?></th>
									<th>R$ <?php echo number_format($dados["MV_VLV"],2, ',', '.') ?></th>
								</tr>
								<?php
								$total += $dados['MV_VLV'];
							}
						}
					?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="5" class="text-right">Total:</th>
								<th><?= "R$ ".number_format($total,2, ',', '.') ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</body>
</html>