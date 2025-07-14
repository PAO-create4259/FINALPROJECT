<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../inc/fonction.php");
$categorie = isset($_GET['categorie']) ? trim($_GET['categorie']) : '';
$_SESSION['categorie'] = $categorie;
header('Location: ../pages/filtre.php');
exit;
?>