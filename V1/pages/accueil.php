<?php
include("../inc/connexion.php");


$stmt = "SELECT id_categorie, nom_categorie FROM final_project_categorie_objet";
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categorie_filter = isset($_GET['categorie']) ? $_GET['categorie'] : '';
$sql = "SELECT o.id_objet, o.nom_objet, c.nom_categorie, m.nom AS proprietaire, 
               e.date_retour, i.nom_image
        FROM final_project_objet o
        JOIN final_project_categorie_objet c ON o.id_categorie = c.id_categorie
        JOIN final_project_membre m ON o.id_membre = m.id_membre
        LEFT JOIN final_project_emprunt e ON o.id_objet = e.id_objet 
        AND (e.date_retour IS NULL OR e.date_retour > CURDATE())
        LEFT JOIN final_project_images_objet i ON o.id_objet = i.id_objet";
if ($categorie_filter) {
    $sql .= " WHERE o.id_categorie = :categorie";
}
$stmt = $conn->prepare($sql);
if ($categorie_filter) {
    $stmt->bindParam(':categorie', $categorie_filter, PDO::PARAM_INT);
}
$stmt->execute();
$objects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Plateforme d'Emprunt d'Objets</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; }
        .form-group select { padding: 8px; width: 200px; }
        .btn { padding: 8px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f4f4f4; }
        img { max-width: 100px; height: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Liste des Objets</h2>
        <form method="GET" action="accueil.php">
            <div class="form-group">
                <label for="categorie">Filtrer par catégorie</label>
                <select id="categorie" name="categorie">
                    <option value="">Toutes</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['id_categorie']); ?>" 
                                <?php echo $categorie_filter == $cat['id_categorie'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nom_categorie']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn">Filtrer</button>
            </div>
        </form>

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
                                    Pas d'image
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($obj['nom_objet']); ?></td>
                            <td><?php echo htmlspecialchars($obj['nom_categorie']); ?></td>
                            <td><?php echo htmlspecialchars($obj['proprietaire']); ?></td>
                            <td>
                                <?php echo $obj['date_retour'] ? htmlspecialchars($obj['date_retour']) : ($obj['date_retour'] === null && $obj['id_objet'] ? 'En cours' : 'Disponible'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Optional: Client-side filtering (backup or enhancement)
        function filterObjects() {
            const categorie = document.getElementById('categorie').value;
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const categorieCell = row.cells[2].textContent;
                row.style.display = categorie ? 
                    (categorieCell === document.querySelector(`#categorie option[value="${categorie}"]`).textContent ? '' : 'none') : 
                    '';
            Ascending
            });
        }

        // Trigger filter on page load if category is pre-selected
        <?php if ($categorie_filter): ?>
            filterObjects();
        <?php endif; ?>
    </script>
</body>
</html>
```