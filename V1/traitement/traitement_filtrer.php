<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../inc/fonction.php");

// Get the selected category from the form
$categorie = isset($_GET['categorie']) ? trim($_GET['categorie']) : '';

// Store the category in the session
$_SESSION['categorie'] = $categorie;

// Redirect to accueil.php
header('Location: ../pages/accueil.php');
exit;
?>