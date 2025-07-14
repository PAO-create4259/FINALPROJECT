<?php
session_start();
include("../inc/fonction.php");
$conn = dbconnect();


if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}


$nom_objet = trim($_POST['nom_objet'] ?? '');
$id_categorie = (int)($_POST['id_categorie'] ?? 0);
$id_membre = (int)($_POST['id_membre'] ?? 0);
$errors = [];

if (!$nom_objet || !$id_categorie || !$id_membre) {
    $errors[] = "Données du formulaire incomplètes.";
}


if (empty($errors)) {
    $sql = "INSERT INTO final_project_objet (nom_objet, id_categorie, id_membre) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'sii', $nom_objet, $id_categorie, $id_membre);
        if (!mysqli_stmt_execute($stmt)) {
            $errors[] = "Erreur lors de l'insertion de l'objet : " . mysqli_stmt_error($stmt);
        }
        $id_objet = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
    } else {
        $errors[] = "Erreur de préparation de la requête : " . mysqli_error($conn);
    }
}

$upload_dir = '../Uploads';
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
$max_file_size = 5 * 1024 * 1024; // 5MB

if (empty($errors) && !empty($_FILES['images']['name'][0])) {
    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
        $errors[] = "Impossible de créer le dossier Uploads.";
    } elseif (!is_writable($upload_dir)) {
        $errors[] = "Le dossier Uploads n'est pas accessible en écriture.";
    } else {
        foreach ($_FILES['images']['name'] as $key => $name) {
            if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) {
                $errors[] = "Erreur d'upload pour $name (code : " . $_FILES['images']['error'][$key] . ").";
                continue;
            }

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_extensions)) {
                $errors[] = "Extension non autorisée pour $name.";
                continue;
            }
            if ($_FILES['images']['size'][$key] > $max_file_size) {
                $errors[] = "Fichier $name trop volumineux.";
                continue;
            }

            $filename = uniqid() . '.' . $ext;
            $target_path = $upload_dir . $filename;
            $db_filename = 'Uploads/' . $filename;

            if (!move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_path)) {
                $errors[] = "Échec de l'upload de $name.";
                continue;
            }

            $sql_image = "INSERT INTO final_project_images_objet (id_objet, nom_image) VALUES (?, ?)";
            $stmt_image = mysqli_prepare($conn, $sql_image);
            if ($stmt_image) {
                mysqli_stmt_bind_param($stmt_image, 'is', $id_objet, $db_filename);
                if (!mysqli_stmt_execute($stmt_image)) {
                    $errors[] = "Erreur lors de l'insertion de l'image $db_filename : " . mysqli_stmt_error($stmt_image);
                }
                mysqli_stmt_close($stmt_image);
            } else {
                $errors[] = "Erreur de préparation de la requête image : " . mysqli_error($conn);
            }
        }
    }
} elseif (empty($_FILES['images']['name'][0])) {
    $errors[] = "Aucune image uploadée.";
}

mysqli_close($conn);


if ($errors) {
    header("Location: ../pages/ajout.php?error=" . urlencode(implode(" ", $errors)));
} else {
    header("Location: ../pages/accueil.php?success=" . urlencode("Objet ajouté avec succès."));
}
exit;
?>