<?php
session_start();
include("../inc/fonction.php");
$conn = dbconnect();

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$nom_objet = isset($_POST['nom_objet']) ? trim($_POST['nom_objet']) : '';
$id_categorie = isset($_POST['id_categorie']) ? (int)$_POST['id_categorie'] : 0;
$id_membre = isset($_POST['id_membre']) ? (int)$_POST['id_membre'] : 0;

if ($nom_objet && $id_categorie && $id_membre) {
    $sql = "INSERT INTO final_project_objet (nom_objet, id_categorie, id_membre) VALUES ('%s', '%s', '%s')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sii', $nom_objet, $id_categorie, $id_membre);
    mysqli_stmt_execute($stmt);
    $id_objet = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    $upload_dir = '../Uploads/';
    $display_dir = '../pages/images-projetfinal/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    if (!is_dir($display_dir)) {
        mkdir($display_dir, 0755, true);
    }

    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $name) {
            if ($_FILES['images']['error'][$key] == 0) {
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $ext;
                $target_path = $upload_dir . $filename;
                $display_path = $display_dir . $filename;

                if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_path)) {
                    copy($target_path, $display_path);

                    $sql_image = "INSERT INTO final_project_images_objet (id_objet, nom_image) VALUES (?, ?)";
                    $stmt_image = mysqli_prepare($conn, $sql_image);
                    mysqli_stmt_bind_param($stmt_image, 'is', $id_objet, $filename);
                    mysqli_stmt_execute($stmt_image);
                    mysqli_stmt_close($stmt_image);
                }
            }
        }
    }
}

mysqli_close($conn);
header('Location: ../pages/accueil.php');
exit;
?>