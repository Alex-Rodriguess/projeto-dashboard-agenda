<?php
include ("config.php");
$date = date_parse($_GET["id"]);
if (isset($_GET["id"]) and $date["year"] == true and $date["month"] == true and $date["day"] == true) {
    if(isset($_SESSION['sqlPagamento'])){
        $sql = $_SESSION['sqlPagamento'];
    }else {
   $sql = "SELECT *
                FROM SCCMOV AS M
                WHERE  M.`MV_CDES` = 1 AND
				       M.`MV_PGT` = 0 AND
                       M.`MV_VCT` = " . $_GET["id"];

    }
	$rs = $mysql -> query($sql);
	$nums = mysqli_num_rows($rs);
} else {
	$rs = NULL;
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Consulta Providencias a Pagar</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="css/cupertino/jquery-ui.min.css">
		<script src="js/jquery-1.10.2.js"></script>
		<script src="js/jquery-ui-1.10.4.custom.js"></script>
		<script src="js/i18n/jquery.ui.datepicker-pt-BR.js"></script>
		<script src="js/bootstrap.js"></script>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<link href="img/favicon.ico" rel="shortcut icon" type="image/x-icon" />
		<script language="JavaScript" type="text/javascript">
			window.onload = function() {
				window.print();
			}
		</script>
	</head>
	<body style="white-space: nowrap;">
		<?php
		if ($rs == NULL or $nums == 0) {
			?>
			<div class="text-center alert-danger">Nenhum registro encontrado</div>
			<?php
			} else {
		?>
		<table cellspacing="1" class="table table-bordered">
			
			<tbody>
				<?php
				$total = 0;

				$sccbco = new sccbco($mysql);
					while($dados = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
						//Soma para a variavel TOTAL
						$total += $dados["MV_VLV"];
						//Retorna os dados do cliente
						$mv_cliente = $mysql->consult("SCCCAD", $dados["MV_CAD"], "`SC_CAD`");
					?>
					<tr class="initialism">
						<th><?php echo $dados["MV_NUM"] ?></th>
						<th><?php echo $dados["MV_PAR"] ?></th>
						<th>
                            <?php
                            $date = new DateTime($dados["MV_VCT"]);
							echo $date -> format('d/m/Y');
                            ?>
                        </th>
						<th><?php
						if ($dados["MV_PGT"] == 0) {
							echo "Pendente de pagamento";
						} else {
							$date = new DateTime($dados["MV_PGT"]);
							echo $date -> format('d/m/Y');
						}
						?></th>
						<th><?php echo $mv_cliente["SC_NOM"] ?></th>
						<th><?= $sccbco->getBco($dados["MV_BCO"]) ?></th>
						<th>R$ <?php echo number_format($dados["MV_VLV"],2, ',', '.') ?></th>
					</tr>
					
					<?php
					}
				?>
			</tbody>
			<thead>
				<tr>
					<th>Nº da Duplicata</th>
					<th>Parcela</th>
					<th>Data Vencimento</th>
					<th>Data Pago</th>
					<th>Cliente</th>
					<th>Banco</th>
					<th>Valor</th>
				</tr>
			</thead>
			<tfoot>
				<tr class="<?php echo $class_css ?>">
					<th colspan="6" class="text-right">Total:</th>
					<th>R$ <?php echo number_format($total,2, ',', '.') ?></th>
				</tr>
			</tfoot>
		</table>
		<?php
		}
	?>
	</body>
</html>