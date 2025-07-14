<?php
require("../inc/fonction.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Plateforme d'Emprunt</title>
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
        .inscription {
            max-width: 500px;
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
        .form-control {
            border-radius: 10px;
            padding: 12px;
            font-size: 1rem;
            margin-bottom: 15px;
        }
        .form-control:focus {
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
        .error {
            color: #dc3545;
            font-size: 0.9rem;
            margin-bottom: 15px;
            text-align: center;
        }
        .inscri {
            color: #007bff;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .inscri:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        @media (max-width: 576px) {
            .inscription {
                padding: 20px;
                margin: 15px;
            }
            h1 {
                font-size: 1.5rem;
            }
            .form-control, .btn-primary {
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
                        <a class="nav-link" href="login.php">Se connecter</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="inscription.php">S'inscrire</a>
                    </li>
                    <?php if (isset($_SESSION['email'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Se déconnecter</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="inscription">
            <h1>Inscription</h1>
            <form action="../traitement/traitement_inscription.php" method="post">
                <div class="mb-3">
                    <label for="nom" class="form-label fw-bold">Nom</label>
                    <input type="text" name="nom" id="nom" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="ddns" class="form-label fw-bold">Date de naissance</label>
                    <input type="date" name="ddns" id="ddns" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="ville" class="form-label fw-bold">Ville</label>
                    <input type="text" name="ville" id="ville" class="form-control" required>
                </div>
                <?php if (isset($_GET['error'])): ?>
                    <p class="error">Votre email a déjà été utilisé</p>
                <?php endif; ?>
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input type="text" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="mdp" class="form-label fw-bold">Mot de passe</label>
                    <input type="password" name="mdp" id="mdp" class="form-control" required>
                </div>
                <?php if (isset($_GET['errormdp'])): ?>
                    <p class="error">Veuillez confirmer votre mot de passe</p>
                <?php endif; ?>
                <div class="mb-3">
                    <label for="mdpbis" class="form-label fw-bold">Confirmer mot de passe</label>
                    <input type="password" name="mdpbis" id="mdpbis" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </form>
            <p class="text-center mt-3">Vous avez déjà un compte ? <a href="login.php" class="inscri">Se connecter</a></p>
        </div>
    </main>
    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>