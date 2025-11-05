<?php
include ("config.php");
//Verifica se POST contem algum valor e se a variavel entrar está com conteudo
if (!empty($_POST) and $_POST["entrar"] == "Entrar") {
	//Se o login e senha estiverem certo, grava a sessão e redireciona para o consultaBanco.php
	if ($_POST["login"] == $user and $_POST["senha"] == $senha) {
		$_SESSION["log"] = $user;
		$_SESSION["pass"] = $senha;
		header("Location: consultaBanco.php");
	} else {
		//Caso contrario, setar variavel de erro e destruir sessão e desetar variaveis
		$erro = "Login e/ou Senha errados!";
		session_unset($_SESSION);
		session_destroy();
	}
	//Se login e senha já estiverem setados e com o mesmo conteudo da variavel user e senha, redireciona para o consulta banco
} elseif (isset($_SESSION["log"]) and $_SESSION["log"] == $user and isset($_SESSION["senha"]) and $_SESSION["senha"] == $senha) {
	header("Location: consultaBanco.php");
}
?><!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Tela de Login</title>
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/style.css" rel="stylesheet">
		<link href="img/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	</head>
	<body>
		<div class="container">
			<form name="form" class="form-signin" role="form" method="post" action="login.php">
				<h2 class="form-signin-heading">Login</h2><?php
				if (isset($erro)) {
					echo "<h3>" . $erro . "</h3>";
				}
				?>
				<input name="login" type="text" class="form-control" placeholder="Login" required autofocus>
				<input name="senha" type="password" class="form-control" placeholder="Senha" required>
				<input name="entrar" class="btn btn-lg btn-primary btn-block" type="submit" value="Entrar" />
			</form>

		</div>
	</body>
</html>
