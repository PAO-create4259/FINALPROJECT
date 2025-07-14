<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

    session_start();
    include("../inc/fonction.php");
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];
    if(to_log($email, $mdp) < 1){
        header('Location: ../pages/login.php?error=0');
        exit;
    }
    $_SESSION['email'] = $email;
    $_SESSION['mdp'] = $mdp;
    //echo "Connexion réussie";
    header('Location: ../pages/accueil.php');
?>