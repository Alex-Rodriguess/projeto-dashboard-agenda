<?php
    include ("config.php");
//Se as variaveis de login e senha não estão setadas e estão diferentes das
// variaveis, redireciona pra tela de login
if ((!isset($_SESSION["log"]) or $_SESSION["log"] <> $user) and (!isset($_SESSION["senha"]) or $_SESSION["senha"] <> $senha)) {
	header("Location: login.php");
}
if (isset($_GET['id']) AND is_numeric($_GET['id'])) {
	$mysql->delete('cxaprev', $_GET['id'], '`PRE_COD`');
	header("Location: agendaPrevisoes.php");
} else {
	header("Location: agendaPrevisoes.php");
}
?>