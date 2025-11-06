<?php
include ("config.php");
if (!isset($_SESSION["log"]) and !isset($_SESSION["pass"])) {
	header("Location: login.php");
} else {
	header("Location: dashboard.php");
}