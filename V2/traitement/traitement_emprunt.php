<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../inc/fonction.php");

if (!isset($_SESSION['email'])) {
    header('Location: ../pages/login.php');
    exit;
}

$conn = dbconnect();
$email = mysqli_real_escape_string($conn, $_SESSION['email']);

$sql_membre = "SELECT id_membre FROM final_project_membre WHERE email = '$email'";
$result_membre = mysqli_query($conn, $sql_membre);
$membre = mysqli_fetch_assoc($result_membre);

if (!$membre) {
    header('Location: ../pages/accueil.php?error=user_not_found');
    exit;
}
$id_membre = $membre['id_membre'];

if (isset($_POST['action']) && $_POST['action'] == 'emprunter') {
    if (!isset($_POST['id_objet'], $_POST['jours']) || !is_numeric($_POST['id_objet']) || !is_numeric($_POST['jours'])) {
        header('Location: ../pages/accueil.php?error=invalid_input');
        exit;
    }

    $id_objet = mysqli_real_escape_string($conn, $_POST['id_objet']);
    $jours = (int)$_POST['jours'];

    if ($jours < 1 || $jours > 30) {
        header('Location: ../pages/accueil.php?error=invalid_days');
        exit;
    }

    $sql_check = "SELECT id_emprunt FROM final_project_emprunt WHERE id_objet = '$id_objet' AND date_retour IS NULL";
    $result_check = mysqli_query($conn, $sql_check);
    if (mysqli_num_rows($result_check) > 0) {
        header('Location: ../pages/accueil.php?error=object_unavailable');
        exit;
    }

    $date_emprunt = date('Y-m-d');
    $sql = "INSERT INTO final_project_emprunt (id_objet, id_membre, date_emprunt) VALUES ('$id_objet', '$id_membre', '$date_emprunt')";
    if (mysqli_query($conn, $sql)) {
        header('Location: ../pages/accueil.php?success=emprunt_added');
    } else {
        header('Location: ../pages/accueil.php?error=emprunt_failed');
    }
}

if (isset($_POST['action']) && $_POST['action'] == 'retourner') {
    if (!isset($_POST['id_emprunt'], $_POST['condition']) || !is_numeric($_POST['id_emprunt']) || !in_array($_POST['condition'], ['ok', 'abime'])) {
        header('Location: ../pages/fiche_membre.php?error=invalid_return');
        exit;
    }

    $id_emprunt = mysqli_real_escape_string($conn, $_POST['id_emprunt']);
    $condition = mysqli_real_escape_string($conn, $_POST['condition']);
    $date_retour = date('Y-m-d');

    $sql = "UPDATE final_project_emprunt SET date_retour = '$date_retour', condition_retour = '$condition' WHERE id_emprunt = '$id_emprunt' AND id_membre = '$id_membre' AND date_retour IS NULL";
    if (mysqli_query($conn, $sql) && mysqli_affected_rows($conn) > 0) {
        header('Location: ../pages/fiche_membre.php?success=retour_added');
    } else {
        header('Location: ../pages/fiche_membre.php?error=retour_failed');
    }
}

mysqli_close($conn);
exit;
?>