<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

    session_start();
    include("../inc/fonction.php");
    $nom = $_POST['nom'];
    $ddns = $_POST['ddns'];
    $email = $_POST['email'];
    $ville = $_POST['ville'];
    if(verify_inscription($email) > 0){
        header('Location: ../pages/login.php?error=0');
        exit;
    }
    $_SESSION['email'] = $email;
    if(verify_password($_POST['mdp'] ,$_POST['mdpbis']) == true){
        $mdp = $_POST['mdp'];
    }
    else{
        header('Location: ../pages/login.php?errormdp=0');
        exit;
    }
    $_SESSION['mdp'] = $mdp;
    add_new_member($email, $mdp, $nom, $ddns,$ville);
    header('Location: ../pages/login.php');
?>