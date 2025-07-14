<?php
    require("../inc/fonction.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>RESEAU</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
    <body>
        <header>
            <h1>Inscription</h1>
        </header>
        <main>
            <div class="inscription">
                <form action="../traitement/traitement_inscription.php" method="post">
                    <p>Nom : <input type="text" name="nom"></p>
                    <p>Date de naissance : <input type="date" name="ddns"></p>
                     <p>Ville: <input type="text" name="ville"></p>
                    
                    <?php if(isset($_GET['error'])) { ?>
                        <p class="error">Votre email a déjà été utilisé</p>
                    <?php } ?>
                    <p>Email : <input type="text" name="email"></p>
                    <p>Mot de passe : <input type="password" name="mdp"></p>
                    <?php if(isset($_GET['errormdp'])) { ?>
                        <p>Veillez confirmer votre mot de passe</p>
                    <?php } ?>
                    <p>Confirmer mot de passe : <input type="password" name="mdpbis"></p>
                    <p><input type="submit" value="S'inscrire"></p>
                </form>
                <p>Vous avez déjà un compte ? <a href="accueil.php" class="inscri">Se connecter</a></p>
            </div>
        </main>
    </body>
</html>