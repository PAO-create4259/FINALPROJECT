<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../inc/fonction.php");
$conn = dbconnect();

$sql_categories = "SELECT id_categorie, nom_categorie FROM final_project_categorie_objet";
$res_categories = mysqli_query($conn, $sql_categories);
$categories = [];
if ($res_categories && mysqli_num_rows($res_categories) > 0) {
    while ($row = mysqli_fetch_assoc($res_categories)) {
        $categories[] = $row;
    }
}
$sql = "SELECT o.id_objet, o.nom_objet, c.nom_categorie, m.nom AS proprietaire, i.nom_image, e.date_retour
        FROM final_project_objet o
        JOIN final_project_categorie_objet c ON o.id_categorie = c.id_categorie
        JOIN final_project_membre m ON o.id_membre = m.id_membre
        LEFT JOIN final_project_images_objet i ON o.id_objet = i.id_objet
        LEFT JOIN final_project_emprunt e ON o.id_objet = e.id_objet AND e.date_retour IS NULL";

$categorie_filter = isset($_SESSION['categorie']) && !empty($_SESSION['categorie']) ? mysqli_real_escape_string($conn, $_SESSION['categorie']) : '';
if ($categorie_filter) {
    $sql .= " WHERE c.nom_categorie = '$categorie_filter'";
}

$res = mysqli_query($conn, $sql);
$objects = [];
if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $objects[] = $row;
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Plateforme d'Emprunt d'Objets</title>
   
</head>
<body>
    <div class="container">
        <h2>Liste des Objets dans la categorie <?=$categorie_filter;?></h2>
       <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Nom de l'objet</th>
                    <th>Catégorie</th>
                    <th>Propriétaire</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($objects)): ?>
                    <tr><td colspan="5">Aucun objet trouvé.</td></tr>
                <?php else: ?>
                    <?php foreach ($objects as $obj): ?>
                        <tr>
                            <td>
                                <?php if ($obj['nom_image']): ?>
                                    <img src="images/<?php echo htmlspecialchars($obj['nom_image']); ?>" alt="<?php echo htmlspecialchars($obj['nom_objet']); ?>">
                                <?php else: ?>
                                    <span class="no-image">Pas d'image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($obj['nom_objet']); ?></td>
                            <td><?php echo htmlspecialchars($obj['nom_categorie']); ?></td>
                            <td><?php echo htmlspecialchars($obj['proprietaire']); ?></td>
                            <td class="<?php echo $obj['date_retour'] === null && $obj['id_objet'] ? 'status-en-cours' : 'status-disponible'; ?>">
                                <?php echo $obj['date_retour'] ? htmlspecialchars($obj['date_retour']) : ($obj['date_retour'] === null && $obj['id_objet'] ? 'En cours' : 'Disponible'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>