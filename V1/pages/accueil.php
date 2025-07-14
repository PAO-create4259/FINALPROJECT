<?php
include("../inc/fonction.php");
session_start();


$categories = option_categorie();
$objects = object_list();
$categorie_filter = isset($_SESSION['categorie']) && !empty($_SESSION['categorie']) ? $_SESSION['categorie'] : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Plateforme d'Emprunt d'Objets</title>
    <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(to bottom, #f8f9fa, #e9ecef); min-height: 100vh; }
        .container { max-width: 1200px; margin-top: 30px; background: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; }
        .navbar { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
        .navbar-brand { font-weight: bold; }
        h2 { color: #343a40; font-weight: 600; }
        .form-select, .btn-primary { border-radius: 5px; }
        .form-select:focus { box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); }
        .btn-primary { transition: background-color 0.3s; }
        .btn-primary:hover { background-color: #0056b3; }
        .table th { background-color: #007bff; color: #fff; }
        .table td, .table th { vertical-align: middle; }
        .table img { max-width: 80px; height: auto; border-radius: 5px; }
        .no-image { color: #6c757d; font-style: italic; }
        .status-en-cours { color: #e67e22; font-weight: bold; }
        .status-disponible { color: #28a745; font-weight: bold; }
        @media (max-width: 576px) {
            .table img { max-width: 60px; }
            h2 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
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
        <h2 class="text-center mb-4">Liste des Objets</h2>
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