<?php
include ("config.php");
$date = date_parse($_GET["id"]);
if (isset($_GET["id"]) and $date["year"] == true and $date["month"] == true and $date["day"] == true) {
    if(isset($_SESSION['sqlPagamento'])){
        $sql = $_SESSION['sqlPagamento'];
        $sqlCXAPREV = $_SESSION['sqlCXAPREV'];
    }else {
        $sql = "SELECT * FROM sccmov WHERE `MV_VCT` = '" . $_GET["id"] . "' AND `MV_PGT` = 0 AND `MV_TIP` >= 1 AND `MV_TIP` <= 4 ORDER BY `MV_VCT`";
        $sqlCXAPREV = "SELECT * FROM cxaprev P, SCCCAD C WHERE PRE_DTA = '". $_GET["id"] ."' AND P.PRE_CAD = C.SC_CAD ORDER BY PRE_CAD asc";
    }
	$rs = $mysql -> query($sql);
	$nums = mysqli_num_rows($rs);

    $rsCXAPREV = $mysql->query($sqlCXAPREV);
    $rowsCXAPREV = mysqli_num_rows($rsCXAPREV);

    $linhas = $nums + $rowsCXAPREV;
} else {
	$rs = NULL;
	$rsCXAPREV = NULL;
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
		if ($linhas == 0) {
			?>
			<div class="text-center alert-danger">Nenhum registro encontrado</div>
			<?php
			} else {
		?>
		<table cellspacing="1" class="table table-bordered">
			
			<tbody>
				<?php
				$total = 0;

                while($row = mysqli_fetch_array($rsCXAPREV, MYSQLI_ASSOC)){
                    $total -= $row["PRE_VLR"];
                    $aa = substr($row["PRE_DTA"], 0, 4);
                    $mm = substr($row["PRE_DTA"], 5, 2);
                    $dd = substr($row["PRE_DTA"], 8, 2);
                    $data = "$dd/$mm/$aa";

                    echo "<tr class='initialism'>";
                    echo "<th></th>"; //n duplicata
                    echo "<th></th>"; //parcela
                    echo "<th>" .$data . "</th>"; //vencimento
                    echo "<th></th>"; //pagmtno
                    echo "<th>" . $row["SC_NOM"] . "</th>"; //cliente
                    echo "<th></th>"; //obs
                    echo "<th></th>"; //bco
                    echo "<th>R$ " .  number_format($row["PRE_VLR"],2, ',', '.') . "</th>"; //vlr
                    echo "</tr>";
                }

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
						<th><?php $date = new DateTime($dados["MV_VCT"]);
							echo $date -> format('d/m/Y');
 ?></th>
						<th>
                            <?php
                                if ($dados["MV_PGT"] == 0) {
                                    echo "Pendente de pagamento";
                                } else {
                                    echo $date = new DateTime($dados["MV_PGT"]);
                                    echo $date -> format('d/m/Y');
                                }
						    ?>
                        </th>
						<th><?= $mv_cliente["SC_NOM"] ?></th>
                        <th><?= $dados["MV_OBS"] ?></th>
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
                    <th>Observação</th>
					<th>Banco</th>
					<th>Valor</th>
				</tr>
			</thead>
			<tfoot>
				<tr class="<?php echo $class_css ?>">
					<th colspan="7" class="text-right">Total:</th>
					<th>R$ <?php echo number_format($total,2, ',', '.') ?></th>
				</tr>
			</tfoot>
		</table>
		<?php
		}
	?>
	</body>
</html>