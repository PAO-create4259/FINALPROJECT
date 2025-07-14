<?php
session_start();
include("../inc/fonction.php");
$conn = dbconnect();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

// Valider les données du formulaire
$nom_objet = isset($_POST['nom_objet']) ? trim($_POST['nom_objet']) : '';
$id_categorie = isset($_POST['id_categorie']) ? (int)$_POST['id_categorie'] : 0;
$id_membre = isset($_POST['id_membre']) ? (int)$_POST['id_membre'] : 0;

// Initialiser un message pour le débogage
$debug_message = '';

if (!$nom_objet || !$id_categorie || !$id_membre) {
    $debug_message = "Erreur : Données du formulaire incomplètes.";
    header("Location: ../pages/ajout.php?error=" . urlencode($debug_message));
    exit;
}

// Insérer l'objet dans la base de données
$sql = "INSERT INTO final_project_objet (nom_objet, id_categorie, id_membre) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    $debug_message = "Erreur de préparation de la requête : " . mysqli_error($conn);
    header("Location: ../pages/ajout.php?error=" . urlencode($debug_message));
    exit;
}
mysqli_stmt_bind_param($stmt, 'sii', $nom_objet, $id_categorie, $id_membre);
if (!mysqli_stmt_execute($stmt)) {
    $debug_message = "Erreur lors de l'insertion de l'objet : " . mysqli_stmt_error($stmt);
    header("Location: ../pages/ajout.php?error=" . urlencode($debug_message));
    exit;
}
$id_objet = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

// Gérer l'upload des images
$upload_dir = '../Uploads/';
$display_dir = '../pages/images/';
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
$max_file_size = 5 * 1024 * 1024; // 5MB

// Créer les dossiers s'ils n'existent pas
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        $debug_message = "Erreur : Impossible de créer le dossier Uploads.";
        header("Location: ../pages/ajout.php?error=" . urlencode($debug_message));
        exit;
    }
}
if (!is_dir($display_dir)) {
    if (!mkdir($display_dir, 0777, true)) {
        $debug_message = "Erreur : Impossible de créer le dossier images.";
        header("Location: ../pages/ajout.php?error=" . urlencode($debug_message));
        exit;
    }
}

// Vérifier les permissions
if (!is_writable($upload_dir) || !is_writable($display_dir)) {
    $debug_message = "Erreur : Les dossiers Uploads ou images ne sont pas accessibles en écriture.";
    header("Location: ../pages/ajout.php?error=" . urlencode($debug_message));
    exit;
}

// Traiter les images
if (!empty($_FILES['images']['name'][0])) {
    foreach ($_FILES['images']['name'] as $key => $name) {
        if ($_FILES['images']['error'][$key] == UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_extensions)) {
                $debug_message .= "Erreur : Extension non autorisée pour $name. ";
                continue;
            }
            if ($_FILES['images']['size'][$key] > $max_file_size) {
                $debug_message .= "Erreur : Fichier $name trop volumineux. ";
                continue;
            }

            $filename = uniqid() . '.' . $ext;
            $target_path = $upload_dir . $filename;
            $display_path = $display_dir . $filename;
            $db_filename = 'Uploads/' . $filename; // Chemin complet pour la base de données

            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_path)) {
                if (!copy($target_path, $display_path)) {
                    $debug_message .= "Erreur : Impossible de copier $filename vers images/. ";
                    continue;
                }

                // Insérer l'image avec le chemin complet dans la base de données
                $sql_image = "INSERT INTO final_project_images_objet (id_objet, nom_image) VALUES (?, ?)";
                $stmt_image = mysqli_prepare($conn, $sql_image);
                if ($stmt_image) {
                    mysqli_stmt_bind_param($stmt_image, 'is', $id_objet, $db_filename);
                    if (!mysqli_stmt_execute($stmt_image)) {
                        $debug_message .= "Erreur lors de l'insertion de l'image $db_filename : " . mysqli_stmt_error($stmt_image);
                    }
                    mysqli_stmt_close($stmt_image);
                } else {
                    $debug_message .= "Erreur de préparation de la requête image : " . mysqli_error($conn);
                }
            } else {
                $debug_message .= "Erreur : Échec de l'upload de $name. ";
            }
        } else {
            $debug_message .= "Erreur d'upload pour $name (code d'erreur : " . $_FILES['images']['error'][$key] . "). ";
        }
    }
} else {
    $debug_message .= "Aucune image uploadée. ";
}

mysqli_close($conn);

// Rediriger avec message de succès ou d'erreur
if ($debug_message) {
    header("Location: ../pages/ajout.php?error=" . urlencode($debug_message));
} else {
    header("Location: ../pages/accueil.php?success=" . urlencode("Objet ajouté avec succès."));
}
exit;
?>