<?php
session_start();
include("../inc/fonction.php");
$conn = dbconnect();

if (!isset($_SESSION['email']) && !isset($_GET['id_membre'])) {
    header('Location: login.php');
    exit;
}

$id_membre = isset($_GET['id_membre']) ? (int)$_GET['id_membre'] : null;
if (!$id_membre) {
    $sql_user = "SELECT id_membre, nom, email, ville, date_naissance FROM final_project_membre WHERE email = ?";
    $stmt_user = mysqli_prepare($conn, $sql_user);
    mysqli_stmt_bind_param($stmt_user, 's', $_SESSION['email']);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);
    $user = mysqli_fetch_assoc($result_user);
    $id_membre = $user['id_membre'];
    mysqli_stmt_close($stmt_user);
} else {
    $sql_user = "SELECT nom, email, ville, date_naissance FROM final_project_membre WHERE id_membre = ?";
    $stmt_user = mysqli_prepare($conn, $sql_user);
    mysqli_stmt_bind_param($stmt_user, 'i', $id_membre);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);
    $user = mysqli_fetch_assoc($result_user);
    mysqli_stmt_close($stmt_user);
}

if (!$user) {
    header('Location: accueil.php');
    exit;
}

// Get member's objects grouped by category
$sql_objects = "SELECT o.id_objet, o.nom_objet, c.nom_categorie, i.nom_image, e.date_retour
                FROM final_project_objet o
                JOIN final_project_categorie_objet c ON o.id_categorie = c.id_categorie
                LEFT JOIN final_project_images_objet i ON o.id_objet = i.id_objet AND i.id_image = (
                    SELECT MIN(id_image) FROM final_project_images_objet WHERE id_objet = o.id_objet
                )
                LEFT JOIN final_project_emprunt e ON o.id_objet = e.id_objet AND e.date_retour IS NULL
                WHERE o.id_membre = ?
                ORDER BY c.nom_categorie, o.nom_objet";
$stmt_objects = mysqli_prepare($conn, $sql_objects);
mysqli_stmt_bind_param($stmt_objects, 'i', $id_membre);
mysqli_stmt_execute($stmt_objects);
$result_objects = mysqli_stmt_get_result($stmt_objects);
$objects_by_category = [];
while ($row = mysqli_fetch_assoc($result_objects)) {
    $category = $row['nom_categorie'];
    if (!isset($objects_by_category[$category])) {
        $objects_by_category[$category] = [];
    }
    $objects_by_category[$category][] = $row;
}
mysqli_stmt_close($stmt_objects);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Membre - Plateforme d'Emprunt</title>
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
        .table img {
            max-width: 80px;
            height: auto;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            transition: transform 0.2s ease;
        }
        .table img:hover {
            transform: scale(1.1);
        }
        .object-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        .object-link:hover {
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
            .table img {
                max-width: 60px;
            }
            h1, h3 {
                font-size: 1.4rem;
            }
            .navbar-brand {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="accueil.php">Plateforme d'Emprunt</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                      <li class="nav-item">
                        <a class="nav-link" href="accueil.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Se connecter</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="inscription.php">S'inscrire</a>
                    </li>
                    <?php if (isset($_SESSION['email'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="membre.php">Mon Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../traitement/traitement_logout.php">Se déconnecter</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Profil de <?php echo htmlspecialchars($user['nom']); ?></h1>
        <div class="mb-4">
            <h3>Informations</h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Ville:</strong> <?php echo htmlspecialchars($user['ville']); ?></p>
            <p><strong>Date de naissance:</strong> <?php echo htmlspecialchars($user['date_naissance']); ?></p>
        </div>
        <div>
            <h3>Objets du Membre</h3>
            <?php if (empty($objects_by_category)): ?>
                <p>Aucun objet enregistré.</p>
            <?php else: ?>
                <?php foreach ($objects_by_category as $category => $objects): ?>
                    <h4><?php echo htmlspecialchars($category); ?></h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Nom de l'objet</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($objects as $obj): ?>
                                    <tr>
                                        <td>
                                            <?php if ($obj['nom_image']): ?>
                                                <img src="images/<?php echo htmlspecialchars($obj['nom_image']); ?>" alt="<?php echo htmlspecialchars($obj['nom_objet']); ?>">
                                            <?php else: ?>
                                                <img src="images/default.jpg" alt="Image par défaut" class="no-image">
                                            <?php endif; ?>
                                        </td>
                                        <td><a href="objet_details.php?id_objet=<?php echo $obj['id_objet']; ?>" class="object-link"><?php echo htmlspecialchars($obj['nom_objet']); ?></a></td>
                                        <td class="<?php echo $obj['date_retour'] === null && $obj['id_objet'] ? 'status-en-cours' : 'status-disponible'; ?>">
                                            <?php echo $obj['date_retour'] ? htmlspecialchars($obj['date_retour']) : ($obj['date_retour'] === null && $obj['id_objet'] ? 'En cours' : 'Disponible'); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>