<?php
    include("../inc/connexion.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FAKEBOOK</title>
        <link rel="stylesheet" href="../assets/css/fontawesome.css">
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
    <body>
        <header>
            <h1>Se connecter</h1>
        </header>
        <main>
            <div class="log">
                <form action="../traitement/traitement_login.php" method="post">
                    <?php if(isset($_GET['error'])) { ?>
                        <p class="error">Email ou mot de passe incorect</p>
                    <?php } ?>
                    <p>Email : <input type="text" name="email"></p>
                    <p>Mot de passe : <input type="password" name="mdp"></p>
                    <p><input type="submit" value="Se connecter"></p>
                </form>
                <p>Vous n'avez pas de compte ? <a href="inscription.php" class="inscri">S'inscrire</a></p>
            </div>
        </main>
    </body>
</html>