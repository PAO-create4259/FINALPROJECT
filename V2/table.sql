CREATE TABLE final_project_membre (
    id_membre INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    date_naissance DATE,
    genre ENUM('M', 'F', 'Autre') NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    ville VARCHAR(100),
    mdp VARCHAR(255) NOT NULL,
    image_profil VARCHAR(255)
);

CREATE TABLE final_project_categorie_objet (
    id_categorie INT PRIMARY KEY AUTO_INCREMENT,
    nom_categorie VARCHAR(50) NOT NULL
);

CREATE TABLE final_project_objet (
    id_objet INT PRIMARY KEY AUTO_INCREMENT,
    nom_objet VARCHAR(100) NOT NULL,
    id_categorie INT,
    id_membre INT,
    FOREIGN KEY (id_categorie) REFERENCES final_project_categorie_objet(id_categorie),
    FOREIGN KEY (id_membre) REFERENCES final_project_membre(id_membre)
);

CREATE TABLE final_project_images_objet (
    id_image INT PRIMARY KEY AUTO_INCREMENT,
    id_objet INT,
    nom_image VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_objet) REFERENCES final_project_objet(id_objet)
);

CREATE TABLE final_project_emprunt (
    id_emprunt INT PRIMARY KEY AUTO_INCREMENT,
    id_objet INT,
    id_membre INT,
    date_emprunt DATE NOT NULL,
    date_retour DATE,
    FOREIGN KEY (id_objet) REFERENCES final_project_objet(id_objet),
    FOREIGN KEY (id_membre) REFERENCES final_project_membre(id_membre)
);


INSERT INTO final_project_membre (nom, date_naissance, genre, email, ville, mdp, image_profil) VALUES
('Jean Dupont', '1990-05-15', 'M', 'jean.dupont@example.com', 'Paris', 'hashed_password1', 'profil1.jpg'),
('Marie Curie', '1985-11-07', 'F', 'marie.curie@example.com', 'Lyon', 'hashed_password2', 'profil2.jpg'),
('Alex Martin', '1992-03-22', 'M', 'alex.martin@example.com', 'Marseille', 'hashed_password3', 'profil3.jpg'),
('Sophie Lefevre', '1988-09-10', 'F', 'sophie.lefevre@example.com', 'Toulouse', 'hashed_password4', 'profil4.jpg');

INSERT INTO final_project_categorie_objet (nom_categorie) VALUES
('Esthétique'), ('Bricolage'), ('Mécanique'), ('Cuisine');

INSERT INTO final_project_objet (nom_objet, id_categorie, id_membre) VALUES
('Miroir LED', 1, 1), ('Pinceau maquillage', 1, 1), ('Sèche-cheveux', 1, 1),
('Perceuse', 2, 1), ('Marteau', 2, 1), ('Tournevis', 2, 1),
('Clé à molette', 3, 1), ('Pistolet à peinture', 3, 1),
('Mixeur', 4, 1), ('Poêle', 4, 1),
('Palette maquillage', 1, 2), ('Lisseur cheveux', 1, 2),
('Scie sauteuse', 2, 2), ('Niveau à bulle', 2, 2), ('Ponceuse', 2, 2),
('Cric voiture', 3, 2), ('Clé dynamométrique', 3, 2), ('Pompe à vélo', 3, 2),
('Blender', 4, 2), ('Couteau céramique', 4, 2),
('Sèche-ongles', 1, 3), ('Fer à friser', 1, 3), ('Trousse cosmétique', 1, 3),
('Visseuse', 2, 3), ('Mètre ruban', 2, 3),
('Boîte à outils', 3, 3), ('Compresseur', 3, 3), ('Testeur électrique', 3, 3),
('Robot cuisine', 4, 3), ('Plancha', 4, 3),
('Lampe UV', 1, 4), ('Kit manucure', 1, 4),
('Perceuse sans fil', 2, 4), ('Scie circulaire', 2, 4), ('Échelle', 2, 4),
('Clé à choc', 3, 4), ('Meuleuse', 3, 4), ('Tournevis électrique', 3, 4),
('Friteuse', 4, 4), ('Cocotte', 4, 4);

INSERT INTO final_project_images_objet (id_objet, nom_image) VALUES
(1, 'miroir_led.jpg'), (2, 'pinceau.jpg'), (3, 'seche_cheveux.jpg'), (4, 'perceuse.jpg'), 
(5, 'marteau.jpg'), (6, 'tournevis.jpg'), (7, 'cle_molette.jpg'), (8, 'pistolet_peinture.jpg'),
(9, 'mixeur.jpg'), (10, 'poele.jpg'), (11, 'palette.jpg'), (12, 'lisseur.jpg'),
(13, 'scie.jpg'), (14, 'niveau.jpg'), (15, 'ponceuse.jpg'), (16, 'cric.jpg'),
(17, 'cle_dynamo.jpg'), (18, 'pompe.jpg'), (19, 'blender.jpg'), (20, 'couteau.jpg'),
(21, 'seche_ongles.jpg'), (22, 'fer_friser.jpg'), (23, 'trousse.jpg'), (24, 'visseuse.jpg'),
(25, 'metre.jpg'), (26, 'boite_outils.jpg'), (27, 'compresseur.jpg'), (28, 'testeur.jpg'),
(29, 'robot.jpg'), (30, 'plancha.jpg'), (31, 'lampe_uv.jpg'), (32, 'manucure.jpg'),
(33, 'perceuse_sf.jpg'), (34, 'scie_circ.jpg'), (35, 'echelle.jpg'), (36, 'cle_choc.jpg'),
(37, 'meuleuse.jpg'), (38, 'tournevis_elec.jpg'), (39, 'friteuse.jpg'), (40, 'cocotte.jpg');

INSERT INTO final_project_emprunt (id_objet, id_membre, date_emprunt, date_retour) VALUES
(1, 2, '2025-06-01', NULL), (4, 3, '2025-06-05', '2025-06-10'),
(11, 1, '2025-06-07', NULL), (15, 4, '2025-06-10', '2025-06-15'),
(21, 4, '2025-06-12', NULL), (24, 1, '2025-06-15', '2025-06-20'),
(31, 2, '2025-06-20', NULL), (33, 3, '2025-06-22', '2025-06-25'),
(9, 3, '2025-06-25', NULL), (19, 1, '2025-06-27', '2025-07-01');

CREATE VIEW final_project_image_objet AS
SELECT 
    o.id_objet,
    o.nom_objet,
    o.id_categorie,
    c.nom_categorie,
    o.id_membre,
    m.nom AS proprietaire,
    i.nom_image,
    i.id_image,
    ROW_NUMBER() OVER (PARTITION BY o.id_objet ORDER BY i.id_image) AS image_rank
FROM final_project_objet o
JOIN final_project_categorie_objet c ON o.id_categorie = c.id_categorie
JOIN final_project_membre m ON o.id_membre = m.id_membre
LEFT JOIN final_project_images_objet i ON o.id_objet = i.id_objet;