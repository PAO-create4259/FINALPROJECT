<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../inc/fonction.php");
$conn = dbconnect();
$id_objet = mysqli_real_escape_string($conn, $_GET['id_objet']);


$sql = "SELECT o.nom_objet, i.nom_image 
        FROM final_project_objet o 
        LEFT JOIN final_project_images_objet i ON o.id_objet = i.id_objet 
        WHERE o.id_objet = '$id_objet'";
$result = mysqli_query($conn, $sql);
$object = mysqli_fetch_assoc($result);



mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emprunter - Plateforme d'Emprunt d'Objets</title>
    <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet">
  
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
        <h2 class="text-center">Emprunter : <?php echo htmlspecialchars($object['nom_objet']); ?></h2>
        <div class="text-center mb-3">
            <?php if ($object['nom_image']): ?>
                <img src="images/<?php echo htmlspecialchars($object['nom_image']); ?>" alt="<?php echo htmlspecialchars($object['nom_objet']); ?>">
            <?php else: ?>
                <span class="no-image">Pas d'image</span>
            <?php endif; ?>
        </div>
        <form action="../traitement/traitement_emprunt.php" method="POST">
            <input type="hidden" name="id_objet" value="<?php echo htmlspecialchars($id_objet); ?>">
            <div class="mb-3">
                <label for="jours" class="form-label fw-bold">Nombre de jours pour l'emprunt</label>
                <input type="number" class="form-control" id="jours" name="jours" min="1" max="30" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Confirmer l'emprunt</button>
            </div>
        </form>
    </div>
    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>