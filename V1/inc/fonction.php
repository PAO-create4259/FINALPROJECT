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
function object_list(){
    $sql = "SELECT o.id_objet, o.nom_objet, c.nom_categorie, m.nom AS proprietaire, i.nom_image, e.date_retour
        FROM final_project_objet o
        JOIN final_project_categorie_objet c ON o.id_categorie = c.id_categorie
        JOIN final_project_membre m ON o.id_membre = m.id_membre
        LEFT JOIN final_project_images_objet i ON o.id_objet = i.id_objet
        LEFT JOIN final_project_emprunt e ON o.id_objet = e.id_objet AND e.date_retour IS NULL";
$res = mysqli_query(dbconnect(), $sql);
$objects = [];
if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $objects[] = $row;
    }
}
return $objects;
}
function option_categorie(){
    $sql_categories = "SELECT id_categorie, nom_categorie FROM final_project_categorie_objet";
$res_categories = mysqli_query(dbconnect(), $sql_categories);
$categories = [];
if ($res_categories && mysqli_num_rows($res_categories) > 0) {
    while ($row = mysqli_fetch_assoc($res_categories)) {
        $categories[] = $row;
    }
}


return $categories;
}
?>