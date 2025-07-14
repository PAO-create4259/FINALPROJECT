<?php
session_start();
include("../inc/fonction.php");
$conn = dbconnect();

if (!isset($_GET['id_objet']) || !is_numeric($_GET['id_objet'])) {
    header('Location: accueil.php');
    exit;
}

$id_objet = (int)$_GET['id_objet'];
$sql = "SELECT o.id_objet, o.nom_objet, c.nom_categorie, m.id_membre, m.nom AS proprietaire, i.nom_image
        FROM final_project_objet o
        JOIN final_project_categorie_objet c ON o.id_categorie = c.id_categorie
        JOIN final_project_membre m ON o.id_membre = m.id_membre
        LEFT JOIN final_project_images_objet i ON o.id_objet = i.id_objet AND i.image_rank = 1
        WHERE o.id_objet = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_objet);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$object = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$object) {
    header('Location: accueil.php');
    exit;
}

$sql_images = "SELECT id_image, nom_image FROM final_project_images_objet WHERE id_objet = ?";
$stmt_images = mysqli_prepare($conn, $sql_images);
mysqli_stmt_bind_param($stmt_images, 'i', $id_objet);
mysqli_stmt_execute($stmt_images);
$result_images = mysqli_stmt_get_result($stmt_images);
$images = [];
while ($row = mysqli_fetch_assoc($result_images)) {
    $images[] = $row;
}
mysqli_stmt_close($stmt_images);

$sql_history = "SELECT e.id_emprunt, e.date_emprunt, e.date_retour, m.id_membre, m.nom AS emprunteur
                FROM final_project_emprunt e
                JOIN final_project_membre m ON e.id_membre = m.id_membre
                WHERE e.id_objet = ?
                ORDER BY e.date_emprunt DESC";
$stmt_history = mysqli_prepare($conn, $sql_history);
mysqli_stmt_bind_param($stmt_history, 'i', $id_objet);
mysqli_stmt_execute($stmt_history);
$result_history = mysqli_stmt_get_result($stmt_history);
$history = [];
while ($row = mysqli_fetch_assoc($result_history)) {
    $history[] = $row;
}
mysqli_stmt_close($stmt_history);

$is_owner = false;
if (isset($_SESSION['email'])) {
    $sql_user = "SELECT id_membre FROM final_project_membre WHERE email = ?";
    $stmt_user = mysqli_prepare($conn, $sql_user);
    mysqli_stmt_bind_param($stmt_user, 's', $_SESSION['email']);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);
    $user = mysqli_fetch_assoc($result_user);
    $is_owner = $user && $user['id_membre'] == $object['id_membre'];
    mysqli_stmt_close($stmt_user);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details de l'Objet - Plateforme d'Emprunt</title>
    <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            font-family: 'Roboto', sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            padding: 30px;
        }
        .navbar {
            background: linear-gradient(to right, #007bff, #0056b3);
            padding: 15px 0;
            border-bottom: 2px solid #004085;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff !important;
        }
        .nav-link {
            color: #ffffff !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #e9ecef !important;
        }
        h1, h3 {
            color: #1a3c6d;
            font-weight: 700;
        }
        .img-primary {
            max-width: 300px;
            height: auto;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: transform 0.2s ease;
        }
        .img-primary:hover {
            transform: scale(1.05);
        }
        .img-secondary {
            max-width: 100px;
            height: auto;
            border-radius: 8px;
            margin: 5px;
            border: 1px solid #e9ecef;
            transition: transform 0.2s ease;
        }
        .img-secondary:hover {
            transform: scale(1.1);
        }
        .btn-danger {
            background: #dc3545;
            border: none;
            border-radius: 8px;
            padding: 8px 15px;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
            background: #ffffff;
        }
        .table th {
            background: #007bff;
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
        }
        .table td, .table th {
            vertical-align: middle;
            padding: 15px;
        }
        .object-link, .emprunteur-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        .object-link:hover, .emprunteur-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        .status-en-cours {
            color: #e67e22;
            font-weight: 600;
        }
        .status-disponible {
            color: #28a745;
            font-weight: 600;
        }
        @media (max-width: 576px) {
            .container {
                padding: 15px;
                margin: 20px auto;
            }
            .img-primary {
                max-width: 200px;
            }
            .img-secondary {
                max-width: 80px;
            }
            h1 {
                font-size: 1.5rem;
            }
            h3 {
                font-size: 1.2rem;
            }
            .navbar-brand {
                font-size: 1.2rem;
            }
            .btn-danger {
                font-size: 0.8rem;
                padding: 6px 10px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="objet_detail.php">Plateforme d'Emprunt</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="accueil.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="total_objet.php">Statistiques des objets</a>
                    </li>
                    <?php if (isset($_SESSION['email'])): ?>
                        <li class="nav-item">
                            <?php
                            $conn = dbconnect();
                            $email = mysqli_real_escape_string($conn, $_SESSION['email']);
                            $sql = "SELECT id_membre FROM final_project_membre WHERE email = '$email'";
                            $result = mysqli_query($conn, $sql);
                            $membre = mysqli_fetch_assoc($result);
                            mysqli_close($conn);
                            ?>
                            <a class="nav-link" href="fiche_membre.php?id_membre=<?php echo htmlspecialchars($membre['id_membre']); ?>">Ma Fiche</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Se deconnecter</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Se connecter</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inscription.php">S'inscrire</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Fiche de l'Objet : <?php echo htmlspecialchars($object['nom_objet']); ?></h1>
        <div class="row">
            <div class="col-md-6 mb-4">
                <h3>Image Principale</h3>
                <img src="images/<?php echo $object['nom_image'] ? htmlspecialchars($object['nom_image']) : 'default.jpg'; ?>" alt="<?php echo htmlspecialchars($object['nom_objet']); ?>" class="img-primary">
            </div>
            <div class="col-md-6 mb-4">
                <h3>Informations</h3>
                <p><strong>Nom :</strong> <?php echo htmlspecialchars($object['nom_objet']); ?></p>
                <p><strong>Categorie :</strong> <?php echo htmlspecialchars($object['nom_categorie']); ?></p>
                <p><strong>Proprietaire :</strong> <a href="fiche_membre.php?id_membre=<?php echo htmlspecialchars($object['id_membre']); ?>" class="object-link"><?php echo htmlspecialchars($object['proprietaire']); ?></a></p>
            </div>
        </div>
        <div class="mb-4">
            <h3>Autres Images</h3>
            <?php if (empty($images)): ?>
                <p>Aucune autre image disponible.</p>
            <?php else: ?>
                <div class="d-flex flex-wrap">
                    <?php foreach ($images as $img): ?>
                        <div class="position-relative m-2">
                            <img src="images/<?php echo htmlspecialchars($img['nom_image']); ?>" alt="Image de l'objet" class="img-secondary">
                            <?php if ($is_owner): ?>
                                <a href="../traitement/traitement_delete_image.php?id_image=<?php echo $img['id_image']; ?>&id_objet=<?php echo $id_objet; ?>" class="btn btn-danger btn-sm position-absolute top-0 end-0" onclick="return confirm('Voulez-vous vraiment supprimer cette image ?');">Supprimer</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div>
            <h3>Historique des Emprunts</h3>
            <?php if (empty($history)): ?>
                <p>Aucun emprunt enregistre.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date d'emprunt</th>
                                <th>Date de retour</th>
                                <th>Emprunteur</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $entry): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($entry['date_emprunt']); ?></td>
                                    <td><?php echo $entry['date_retour'] ? htmlspecialchars($entry['date_retour']) : 'Non retourne'; ?></td>
                                    <td>
                                        <a href="fiche_membre.php?id_membre=<?php echo htmlspecialchars($entry['id_membre']); ?>" class="emprunteur-link">
                                            <?php echo htmlspecialchars($entry['emprunteur']); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>