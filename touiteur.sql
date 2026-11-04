CREATE DATABASE IF NOT EXISTS touiteur;
USE touiteur;

DROP TABLE IF EXISTS Note;
DROP TABLE IF EXISTS Abonnement_Tag;
DROP TABLE IF EXISTS Touite_Tag;
DROP TABLE IF EXISTS Suivre;
DROP TABLE IF EXISTS Tag;
DROP TABLE IF EXISTS Image;
DROP TABLE IF EXISTS Touite;
DROP TABLE IF EXISTS Utilisateur;

CREATE TABLE Utilisateur (
idUtilisateur INT AUTO_INCREMENT,
pseudo VARCHAR(50) NOT NULL,
nom VARCHAR(50),
prenom VARCHAR(50),
email VARCHAR(100) NOT NULL,
nbTouites INT DEFAULT 0 NOT NULL,
CONSTRAINT pk_utilisateur PRIMARY KEY (idUtilisateur),
CONSTRAINT uq_utilisateur_pseudo UNIQUE (pseudo),
CONSTRAINT uq_utilisateur_email UNIQUE (email),
CONSTRAINT ck_utilisateur_nb CHECK (nbTouites >= 0)
);

CREATE TABLE Touite (
idTouite INT AUTO_INCREMENT,
texte VARCHAR(235) NOT NULL,
datePublication DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
idAuteur INT NOT NULL,
CONSTRAINT pk_touite PRIMARY KEY (idTouite),
CONSTRAINT fk_touite_auteur FOREIGN KEY (idAuteur) REFERENCES Utilisateur(idUtilisateur)
);

CREATE TABLE Image (
idImage INT AUTO_INCREMENT,
description VARCHAR(255),
chemin VARCHAR(255) NOT NULL,
idTouite INT NOT NULL,
CONSTRAINT pk_image PRIMARY KEY (idImage),
CONSTRAINT uq_image_touite UNIQUE (idTouite),
CONSTRAINT fk_image_touite FOREIGN KEY (idTouite) REFERENCES Touite(idTouite)
);

CREATE TABLE Tag (
idTag INT AUTO_INCREMENT,
libelle VARCHAR(100) NOT NULL,
description VARCHAR(255),
CONSTRAINT pk_tag PRIMARY KEY (idTag)
);

CREATE TABLE Suivre (
idSuiveur INT NOT NULL,
idSuivi INT NOT NULL,
CONSTRAINT pk_suivre PRIMARY KEY (idSuiveur, idSuivi),
CONSTRAINT fk_suivre_suiveur FOREIGN KEY (idSuiveur) REFERENCES Utilisateur(idUtilisateur),
CONSTRAINT fk_suivre_suivi FOREIGN KEY (idSuivi) REFERENCES Utilisateur(idUtilisateur),
CONSTRAINT ck_suivre_soi CHECK (idSuiveur <> idSuivi)
);

CREATE TABLE Touite_Tag (
idTouite INT NOT NULL,
idTag INT NOT NULL,
CONSTRAINT pk_touite_tag PRIMARY KEY (idTouite, idTag),
CONSTRAINT fk_tt_touite FOREIGN KEY (idTouite) REFERENCES Touite(idTouite),
CONSTRAINT fk_tt_tag FOREIGN KEY (idTag) REFERENCES Tag(idTag)
);

CREATE TABLE Abonnement_Tag (
idUtilisateur INT NOT NULL,
idTag INT NOT NULL,
CONSTRAINT pk_abonnement_tag PRIMARY KEY (idUtilisateur, idTag),
CONSTRAINT fk_at_utilisateur FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur),
CONSTRAINT fk_at_tag FOREIGN KEY (idTag) REFERENCES Tag(idTag)
);

CREATE TABLE Note (
idUtilisateur INT NOT NULL,
idTouite INT NOT NULL,
valeur INT NOT NULL,
CONSTRAINT pk_note PRIMARY KEY (idUtilisateur, idTouite),
CONSTRAINT fk_note_utilisateur FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur),
CONSTRAINT fk_note_touite FOREIGN KEY (idTouite) REFERENCES Touite(idTouite),
CONSTRAINT ck_note_valeur CHECK (valeur IN (-1, 1))
);

INSERT INTO Utilisateur (idUtilisateur, pseudo, nom, prenom, email, nbTouites) VALUES
(1, 'antho', 'Wurpillot', 'Anthony', 'anthony@mail.fr', 1),
(2, 'axel', 'Mare', 'Axel', 'axel@mail.fr', 1),
(3, 'muzaffer', 'Yaman', 'Muzaffer', 'muzaffer@mail.fr', 0);

INSERT INTO Touite (idTouite, texte, datePublication, idAuteur) VALUES
(1, 'Premier touite sur notre réseau !', NOW(), 1),
(2, 'Base MySQL opérationnelle.', NOW(), 2);

INSERT INTO Tag (idTag, libelle, description) VALUES
(1, 'sae', 'Projet de développement web'),
(2, 'php', 'Langage de script serveur');

INSERT INTO Touite_Tag (idTouite, idTag) VALUES
(1, 1),
(1, 2);