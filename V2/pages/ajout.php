<?php
session_start();
include("../inc/fonction.php");
$conn = dbconnect();

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

// Get logged-in user's ID
$sql_user = "SELECT id_membre FROM final_project_membre WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql_user);
mysqli_stmt_bind_param($stmt, 's', $_SESSION['email']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
$id_membre = $user['id_membre'];
mysqli_stmt_close($stmt);

// Get categories
$categories = option_categorie();
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Objet - Plateforme d'Emprunt</title>
    <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            font-family: 'Roboto', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .ajout {
            max-width: 600px;
            width: 100%;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            padding: 30px;
            margin: 20px auto;
        }
        .navbar {
            background: linear-gradient(to right, #007bff, #0056b3);
            padding: 15px 0;
            border-bottom: 2px solid #004085;
            width: 100%;
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
        h1 {
            color: #1a3c6d;
            font-weight: 700;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px;
            font-size: 1rem;
            margin-bottom: 15px;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.4);
            border-color: #007bff;
        }
        .btn-primary {
            background: #20c997;
            border: none;
            font-weight: 600;
            border-radius: 10px;
            padding: 12px;
            width: 100%;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background: #17a589;
            transform: translateY(-2px);
        }
        @media (max-width: 576px) {
            .ajout {
                padding: 20px;
                margin: 15px;
            }
            h1 {
                font-size: 1.5rem;
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
                    <li class="nav-item">
                        <a class="nav-link" href="membre.php">Mon Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../traitement/traitement_logout.php">Se déconnecter</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="ajout">
            <h1>Ajouter un Objet</h1>
            <form action="../traitement/traitement_upload.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_membre" value="<?php echo $id_membre; ?>">
                <div class="mb-3">
                    <label for="nom_objet" class="form-label fw-bold">Nom de l'objet</label>
                    <input type="text" name="nom_objet" id="nom_objet" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="id_categorie" class="form-label fw-bold">Catégorie</label>
                    <select name="id_categorie" id="id_categorie" class="form-select" required>
                        <option value="">Sélectionner une catégorie</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id_categorie']; ?>"><?php echo htmlspecialchars($cat['nom_categorie']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="images" class="form-label fw-bold">Images (plusieurs possibles)</label>
                    <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">Ajouter l'objet</button>
            </form>
        </div>
    </main>
    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>