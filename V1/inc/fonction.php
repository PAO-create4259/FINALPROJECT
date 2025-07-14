<?php
require("connexion.php");
function to_log($email, $mdp) {
    $sql = "SELECT * FROM final_project_membre WHERE email = '%s' AND mdp = '%s'";
    $sql = sprintf($sql, $email, $mdp);
    $result = mysqli_query(dbconnect(), $sql);
    return mysqli_num_rows($result);
}
function verify_inscription($email) {
    $sql = "SELECT * FROM final_project_membre WHERE email = '%s'";
    $sql = sprintf($sql, $email);
    $result = mysqli_query(dbconnect(), $sql);
    return mysqli_num_rows($result);
}
function verify_password($mdp, $mdpbis) {
    return $mdp == $mdpbis;
}
function add_new_member($email, $mdp, $nom, $ddns,$ville) {
    $sql = "INSERT INTO final_project_membre (email, mdp, nom, date_naissance,ville) VALUES ('%s', '%s', '%s', '%s','%s')";
    $sql = sprintf($sql, $email, $mdp, $nom, $ddns,$ville);
    mysqli_query(dbconnect(), $sql);
}
?>