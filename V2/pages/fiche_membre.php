<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../inc/fonction.php");

// Check if id_membre is provided
if (!isset($_GET['id_membre']) || !is_numeric($_GET['id_membre'])) {
    header('Location: accueil.php?error=invalid_member');
    exit;
}

$conn = dbconnect();
$id_membre = mysqli_real_escape_string($conn, $_GET['id_membre']);

// Fetch member details
$sql_membre = "SELECT nom FROM final_project_membre WHERE id_membre = '$id_membre'";
$result_membre = mysqli_query($conn, $sql_membre);
$membre = mysqli_fetch_assoc($result_membre);

if (!$membre) {
    header('Location: accueil.php?error=user_not_found');
    exit;
}
$nom_membre = $membre['nom'];

// Fetch member's loans
$sql_emprunts = "SELECT e.id_emprunt, o.nom_objet, e.date_emprunt, e.date_retour, e.condition_retour, e.id_membre
                 FROM final_project_emprunt e
                 JOIN final_project_objet o ON e.id_objet = o.id_objet
                 WHERE e.id_membre = '$id_membre'
                 ORDER BY e.date_emprunt DESC";
$result_emprunts = mysqli_query($conn, $sql_emprunts);
$emprunts = [];
if ($result_emprunts && mysqli_num_rows($result_emprunts) > 0) {
    while ($row = mysqli_fetch_assoc($result_emprunts)) {
        $emprunts[] = $row;
    }
}

// Check if logged-in user is the same as the selected member
$logged_in_membre = null;
if (isset($_SESSION['email'])) {
    $email = mysqli_real_escape_string($conn, $_SESSION['email']);
    $sql_logged_in = "SELECT id_membre FROM final_project_membre WHERE email = '$email'";
    $result_logged_in = mysqli_query($conn, $sql_logged_in);
    $logged_in_membre = mysqli_fetch_assoc($result_logged_in);
}
$is_owner = $logged_in_membre && $logged_in_membre['id_membre'] == $id_membre;

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Membre - Plateforme d'Emprunt d'Objets</title>
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
        .btn-primary, .btn-success {
            border-radius: 8px;
            padding: 10px;
            border: none;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-success:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
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
        .status-en-cours {
            color: #e67e22;
            font-weight: bold;
        }
        .status-retourne {
            color: #28a745;
            font-weight: bold;
        }
        .alert {
            margin-bottom: 20px;
        }
        @media (max-width: 576px) {
            .container {
                padding: 15px;
            }
            h2 {
                font-size: 1.5rem;
            }
            .btn-primary, .btn-success {
                font-size: 0.9rem;
                padding: 8px;
            }
            .form-select-sm {
                font-size: 0.9rem;
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
                        <a class="nav-link" href="accueil.php">Accueil</a>
                    </li>
                    <?php if (isset($_SESSION['email'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="fiche_membre.php?id_membre=<?php echo htmlspecialchars($logged_in_membre['id_membre']); ?>">Ma Fiche</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="total_objet.php">Statistiques de objets</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../traitement/traitement_logout.php">Se deconnecter</a>
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
        <h2 class="text-center">Fiche de <?php echo htmlspecialchars($nom_membre); ?></h2>
        <?php if (isset($_GET['success']) && $_GET['success'] == 'retour_added'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Objet retourne avec succès !
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <h3 class="mt-4">Historique des Emprunts</h3>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Objet</th>
                        <th>Date d'emprunt</th>
                        <th>Date de retour</th>
                        <th>Condition de retour</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($emprunts)): ?>
                        <tr><td colspan="5" class="text-center">Aucun emprunt trouve.</td></tr>
                    <?php else: ?>
                        <?php foreach ($emprunts as $emprunt): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($emprunt['nom_objet']); ?></td>
                                <td><?php echo htmlspecialchars($emprunt['date_emprunt']); ?></td>
                                <td>
                                    <?php echo $emprunt['date_retour'] ? htmlspecialchars($emprunt['date_retour']) : '<span class="status-en-cours">En cours</span>'; ?>
                                </td>
                                <td>
                                    <?php echo $emprunt['condition_retour'] ? htmlspecialchars($emprunt['condition_retour']) : '-'; ?>
                                </td>
                                <td>
                                    <?php if (!$emprunt['date_retour'] && $is_owner): ?>
                                        <form action="../traitement/traitement_emprunt.php" method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="retourner">
                                            <input type="hidden" name="id_emprunt" value="<?php echo htmlspecialchars($emprunt['id_emprunt']); ?>">
                                            <select name="condition" class="form-select form-select-sm d-inline-block w-auto" required>
                                                <option value="">Choisir...</option>
                                                <option value="ok">Ok</option>
                                                <option value="abime">Abime</option>
                                            </select>
                                            <button type="submit" class="btn btn-success btn-sm">Retourner</button>
                                        </form>
                                    <?php elseif (!$emprunt['date_retour']): ?>
                                        <span class="text-muted">En cours (non retournable)</span>
                                    <?php else: ?>
                                        <span class="status-retourne">Retourne</span>
                                    <?php endif; ?>
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