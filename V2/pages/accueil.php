<?php
include("../inc/fonction.php");
session_start();

$categories = option_categorie();
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$available_only = isset($_GET['available_only']) ? 1 : 0;
$categorie_filter = isset($_SESSION['categorie']) && !empty($_SESSION['categorie']) ? $_SESSION['categorie'] : '';

$objects = object_list($search_name, $available_only, $categorie_filter);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Plateforme d'Emprunt d'Objets</title>
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
        h2 {
            color: #1a3c6d;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
        }
        .form-control, .form-select, .btn-primary {
            border-radius: 10px;
            padding: 12px;
            font-size: 1rem;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.4);
            border-color: #007bff;
        }
        .btn-primary {
            background: #20c997;
            border: none;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background: #17a589;
            transform: translateY(-2px);
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
            letter-spacing: 0.05em;
        }
        .table td, .table th {
            vertical-align: middle;
            padding: 15px;
            border-color: #dee2e6;
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
        .no-image {
            color: #6c757d;
            font-style: italic;
            font-size: 0.9rem;
        }
        .status-en-cours {
            color: #e67e22;
            font-weight: 600;
        }
        .status-disponible {
            color: #28a745;
            font-weight: 600;
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
        @media (max-width: 576px) {
            .container {
                padding: 15px;
                margin: 20px auto;
            }
            .table img {
                max-width: 60px;
            }
            h2 {
                font-size: 1.4rem;
            }
            .form-control, .form-select, .btn-primary {
                font-size: 0.9rem;
                padding: 10px;
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
                            <a class="nav-link" href="login.php">Se déconnecter</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Liste des Objets <?php echo $categorie_filter ? 'dans la catégorie ' . htmlspecialchars($categorie_filter) : ''; ?></h2>
        <?php if (isset($_SESSION['email'])): ?>
            <div class="mb-4 text-center">
                <a href="ajout.php" class="btn btn-primary">Ajouter un objet</a>
            </div>
        <?php endif; ?>
        <form method="GET" action="../traitement/traitement_filtrer.php" class="mb-5">
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
        <form method="GET" action="accueil.php" class="mb-5">
            <div class="row g-3 align-items-center justify-content-center">
                <div class="col-auto">
                    <label for="categorie" class="form-label fw-bold">Catégorie</label>
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
                    <label for="search_name" class="form-label fw-bold">Nom de l'objet</label>
                    <input type="text" id="search_name" name="search_name" class="form-control" value="<?php echo htmlspecialchars($search_name); ?>" placeholder="Rechercher...">
                </div>
                <div class="col-auto">
                    <div class="form-check">
                        <input type="checkbox" id="available_only" name="available_only" class="form-check-input" <?php echo $available_only ? 'checked' : ''; ?>>
                        <label for="available_only" class="form-check-label fw-bold">Disponible uniquement</label>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
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
                                        <img src="Uploads/<?php echo htmlspecialchars($obj['nom_image']); ?>" alt="<?php echo htmlspecialchars($obj['nom_objet']); ?>">
                                    <?php else: ?>
                                        <img src="images-projetfinal/vernis1.jpg" alt="Image par défaut" class="no-image">
                                    <?php endif; ?>
                                </td>
                                <td><a href="objet_details.php?id_objet=<?php echo $obj['id_objet']; ?>" class="object-link"><?php echo htmlspecialchars($obj['nom_objet']); ?></a></td>
                                <td><?php echo htmlspecialchars($obj['nom_categorie']); ?></td>
                                <td><a href="membre.php?id_membre=<?php echo $obj['id_membre']; ?>" class="object-link"><?php echo htmlspecialchars($obj['proprietaire']); ?></a></td>
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