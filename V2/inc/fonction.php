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
function object_list($search_name = '', $available_only = 0, $categorie_filter = '') {
    $conn = dbconnect();
    $sql = "
        SELECT 
            o.id_objet,
            o.nom_objet,
            o.nom_categorie,
            o.proprietaire,
            o.nom_image,
            e.date_retour
        FROM final_project_image_objet o
        LEFT JOIN final_project_emprunt e ON o.id_objet = e.id_objet
        WHERE o.image_rank = 1
    ";
    
    $params = [];
    $types = '';
    
    if ($search_name) {
        $sql .= " AND o.nom_objet LIKE ?";
        $params[] = "%$search_name%";
        $types .= 's';
    }
    
    if ($available_only) {
        $sql .= " AND (e.date_retour IS NOT NULL OR e.id_emprunt IS NULL)";
    }
    
    if ($categorie_filter) {
        $sql .= " AND o.nom_categorie = ?";
        $params[] = $categorie_filter;
        $types .= 's';
    }
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $objects = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $objects[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
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