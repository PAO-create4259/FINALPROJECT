<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../inc/fonction.php");

$conn = dbconnect();

$sql_stats = "SELECT condition_retour, COUNT(*) as total
              FROM final_project_emprunt
              WHERE date_retour IS NOT NULL AND condition_retour IN ('ok', 'abime')
              GROUP BY condition_retour";
$result_stats = mysqli_query($conn, $sql_stats);
$stats = ['ok' => 0, 'abime' => 0];
if ($result_stats && mysqli_num_rows($result_stats) > 0) {
    while ($row = mysqli_fetch_assoc($result_stats)) {
        $stats[$row['condition_retour']] = $row['total'];
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - Plateforme d'Emprunt d'Objets</title>
    <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #e9ecef, #f8f9fa);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 600px;
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
        .card {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #007bff;
            color: #ffffff;
            font-weight: 600;
        }
        .card-body {
            font-size: 1.2rem;
        }
        @media (max-width: 576px) {
            .container {
                padding: 15px;
            }
            h2 {
                font-size: 1.5rem;
            }
            .card-body {
                font-size: 1rem;
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
                            <a class="nav-link" href="total_objet.php">Statistiques des objets</a>
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
        <h2 class="text-center">Statistiques des Retours</h2>
        <div class="card">
            <div class="card-header">Objets OK</div>
            <div class="card-body">
                Total : <?php echo $stats['ok']; ?> objets
            </div>
        </div>
        <div class="card">
            <div class="card-header">Objets abimes</div>
            <div class="card-body">
                Total : <?php echo $stats['abime']; ?> objets
            </div>
        </div>
    </div>
    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>