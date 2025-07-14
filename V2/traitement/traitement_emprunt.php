<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../inc/fonction.php");

if (!isset($_SESSION['email'])) {
    header('Location: ../pages/login.php');
    exit;
}

if (!isset($_POST['id_objet'], $_POST['jours']) || !is_numeric($_POST['id_objet']) || !is_numeric($_POST['jours'])) {
    header('Location: ../pages/accueil.php');
    exit;
}

$conn = dbconnect();
$id_objet = mysqli_real_escape_string($conn, $_POST['id_objet']);
$jours = (int)$_POST['jours'];
$id_membre = mysqli_real_escape_string($conn, $_SESSION['email']); 
$date_emprunt = date('Y-m-d');
$date_retour = SUM($date_emprunt,$jours );



$sql_membre = "SELECT id_membre FROM final_project_membre WHERE email = '$id_membre'";
$result_membre = mysqli_query($conn, $sql_membre);
$membre = mysqli_fetch_assoc($result_membre);
if (!$membre) {
    header('Location: ../pages/accueil.php');
    exit;
}
$id_membre = $membre['id_membre'];


$sql = "INSERT INTO final_project_emprunt (id_objet, id_membre, date_emprunt,date_retour) VALUES ('$id_objet', '$id_membre', '$date_emprunt','$date_retour')";
if (mysqli_query($conn, $sql)) {
    header('Location: ../pages/accueil.php');
} else {
    header('Location: ../pages/accueil.php?error=emprunt_failed');
}
mysqli_close($conn);
exit;
?>