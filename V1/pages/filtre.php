<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../inc/fonction.php");
$conn = dbconnect();

$sql_categories = "SELECT id_categorie, nom_categorie FROM final_project_categorie_objet";
$res_categories = mysqli_query($conn, $sql_categories);
$categories = [];
if ($res_categories && mysqli_num_rows($res_categories) > 0) {
    while ($row = mysqli_fetch_assoc($res_categories)) {
        $categories[] = $row;
    }
}
$sql = "SELECT o.id_objet, o.nom_objet, c.nom_categorie, m.nom AS proprietaire, i.nom_image, e.date_retour
        FROM final_project_objet o
        JOIN final_project_categorie_objet c ON o.id_categorie = c.id_categorie
        JOIN final_project_membre m ON o.id_membre = m.id_membre
        LEFT JOIN final_project_images_objet i ON o.id_objet = i.id_objet
        LEFT JOIN final_project_emprunt e ON o.id_objet = e.id_objet AND e.date_retour IS NULL";

$categorie_filter = isset($_SESSION['categorie']) && !empty($_SESSION['categorie']) ? mysqli_real_escape_string($conn, $_SESSION['categorie']) : '';
if ($categorie_filter) {
    $sql .= " WHERE c.nom_categorie = '$categorie_filter'";
}

$res = mysqli_query($conn, $sql);
$objects = [];
if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $objects[] = $row;
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtre - Plateforme d'Emprunt d'Objets</title>
    <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #e9ecef, #f8f9fa);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 1200px;
            margin-top: 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 25px;
        }
        .navbar {
            background: #007bff;
            border-bottom: 3px solid #0056b3;
        }
        .navbar-brand, .nav-link {
            color: #ffffff !important;
            font-weight: 500;
        }
        .navbar-brand:hover, .nav-link:hover {
            color: #e9ecef !important;
        }
        h2 {
            color: #343a40;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .form-select, .btn-primary {
            border-radius: 8px;
            padding: 10px;
        }
        .form-select:focus {
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
            border-color: #007bff;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        .table th {
            background-color: #007bff;
            color: #ffffff;
            font-weight: 600;
        }
        .table td, .table th {
            vertical-align: middle;
            padding: 15px;
        }
        .table img {
            max-width: 80px;
            height: auto;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
        .no-image {
            color: #6c757d;
            font-style: italic;
        }
        .status-en-cours {
            color: #e67e22;
            font-weight: bold;
        }
        .status-disponible {
            color: #28a745;
            font-weight: bold;
        }
        @media (max-width: 576px) {
            .container {
                padding: 15px;
            }
            .table img {
                max-width: 60px;
            }
            h2 {
                font-size: 1.5rem;
            }
            .form-select, .btn-primary {
                font-size: 0.9rem;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="accueil.php">Plateforme d'Emprunt</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Se connecter</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="inscription.php">S'inscrire</a>
                    </li>
                    <?php if (isset($_SESSION['email'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../traitement/traitement_logout.php">Se déconnecter</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-center">Liste des Objets <?php echo $categorie_filter ? 'dans la catégorie ' . htmlspecialchars($categorie_filter) : ''; ?></h2>
        <form method="GET" action="../traitement/traitement_filtrer.php" class="mb-4">
            <div class="row g-3 align-items-center justify-content-center">
                <div class="col-auto">
                    <label for="categorie" class="form-label fw-bold">Filtrer par catégorie</label>
                </div>
                <div class="col-auto">
                    <select id="categorie" name="categorie" class="form-select" aria-label="Catégorie">
                        <option value="">Toutes</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['nom_categorie']); ?>" 
                                    <?php echo $categorie_filter == $cat['nom_categorie'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nom_categorie']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom de l'objet</th>
                        <th>Catégorie</th>
                        <th>Propriétaire</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($objects)): ?>
                        <tr><td colspan="5" class="text-center">Aucun objet trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($objects as $obj): ?>
                            <tr>
                                <td>
                                    <?php if ($obj['nom_image']): ?>
                                        <img src="images/<?php echo htmlspecialchars($obj['nom_image']); ?>" alt="<?php echo htmlspecialchars($obj['nom_objet']); ?>">
                                    <?php else: ?>
                                        <span class="no-image">Pas d'image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($obj['nom_objet']); ?></td>
                                <td><?php echo htmlspecialchars($obj['nom_categorie']); ?></td>
                                <td><?php echo htmlspecialchars($obj['proprietaire']); ?></td>
                                <td class="<?php echo $obj['date_retour'] === null && $obj['id_objet'] ? 'status-en-cours' : 'status-disponible'; ?>">
                                    <?php echo $obj['date_retour'] ? htmlspecialchars($obj['date_retour']) : ($obj['date_retour'] === null && $obj['id_objet'] ? 'En cours' : 'Disponible'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>