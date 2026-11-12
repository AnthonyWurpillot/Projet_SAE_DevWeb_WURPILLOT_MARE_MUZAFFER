-- ============================================================
--  Touiteur.app - Script de creation de la base de donnees
--  Base : touiteur (MySQL / MariaDB)
-- ============================================================

CREATE DATABASE IF NOT EXISTS touiteur;
USE touiteur;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS Note;
DROP TABLE IF EXISTS Abonnement_Tag;
DROP TABLE IF EXISTS Touite_Tag;
DROP TABLE IF EXISTS Suivre;
DROP TABLE IF EXISTS Tag;
DROP TABLE IF EXISTS Image;
DROP TABLE IF EXISTS Touite;
DROP TABLE IF EXISTS Utilisateur;

SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
--  Utilisateurs
-- ------------------------------------------------------------

CREATE TABLE Utilisateur (
    idUtilisateur INT AUTO_INCREMENT PRIMARY KEY,
    pseudo        VARCHAR(50)  NOT NULL UNIQUE,
    nom           VARCHAR(50)  NULL,
    prenom        VARCHAR(50)  NULL,
    email         VARCHAR(100) NOT NULL UNIQUE,
    nbTouites     INT          NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Utilisateur (idUtilisateur, pseudo, nom, prenom, email, nbTouites) VALUES
(1, 'antho',    'Wurpillot', 'Anthony', 'anthony@mail.fr',    2),
(2, 'axel',     'Mare',      'Axel',    'axel@mail.fr',       1),
(3, 'muzaffer', 'Yaman',     'Muzaffer', 'muzaffer@mail.fr',  1);

-- ------------------------------------------------------------
--  Touites
-- ------------------------------------------------------------

CREATE TABLE Touite (
    idTouite        INT AUTO_INCREMENT PRIMARY KEY,
    texte           VARCHAR(235) NOT NULL,
    datePublication DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    idAuteur        INT          NOT NULL,
    CONSTRAINT fk_touite_auteur FOREIGN KEY (idAuteur)
        REFERENCES Utilisateur (idUtilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Touite (idTouite, texte, datePublication, idAuteur) VALUES
(1, 'Premier touite sur notre réseau !', '2026-11-05 14:31:58', 1),
(2, 'Base MySQL opérationnelle.',         '2026-11-05 14:31:58', 2),
(3, 'Je découvre #Java et #php pour ma #sae !', '2026-11-05 14:34:19', 1),
(4, 'Bienvenue sur Touiteur, testez les votes et les tags !', '2026-11-05 15:10:00', 3);

-- ------------------------------------------------------------
--  Tags
-- ------------------------------------------------------------

CREATE TABLE Tag (
    idTag       INT AUTO_INCREMENT PRIMARY KEY,
    libelle     VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Tag (idTag, libelle, description) VALUES
(1, 'sae',  'Projet de développement web'),
(2, 'php',  'Langage de script serveur'),
(3, 'Java', NULL);

-- ------------------------------------------------------------
--  Tables de liaison
-- ------------------------------------------------------------

CREATE TABLE Touite_Tag (
    idTouite INT NOT NULL,
    idTag    INT NOT NULL,
    PRIMARY KEY (idTouite, idTag),
    CONSTRAINT fk_tt_touite FOREIGN KEY (idTouite) REFERENCES Touite (idTouite),
    CONSTRAINT fk_tt_tag    FOREIGN KEY (idTag)    REFERENCES Tag    (idTag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Touite_Tag (idTouite, idTag) VALUES
(1, 1), (1, 2),
(3, 1), (3, 2), (3, 3);

CREATE TABLE Suivre (
    idSuiveur INT NOT NULL,
    idSuivi   INT NOT NULL,
    PRIMARY KEY (idSuiveur, idSuivi),
    CONSTRAINT fk_suivre_suiveur FOREIGN KEY (idSuiveur) REFERENCES Utilisateur (idUtilisateur),
    CONSTRAINT fk_suivre_suivi   FOREIGN KEY (idSuivi)   REFERENCES Utilisateur (idUtilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Suivre (idSuiveur, idSuivi) VALUES
(1, 2),
(2, 1),
(3, 2);

CREATE TABLE Abonnement_Tag (
    idUtilisateur INT NOT NULL,
    idTag         INT NOT NULL,
    PRIMARY KEY (idUtilisateur, idTag),
    CONSTRAINT fk_at_utilisateur FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur (idUtilisateur),
    CONSTRAINT fk_at_tag         FOREIGN KEY (idTag)         REFERENCES Tag         (idTag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Abonnement_Tag (idUtilisateur, idTag) VALUES
(1, 3),
(2, 1),
(3, 2);

CREATE TABLE Note (
    idUtilisateur INT NOT NULL,
    idTouite      INT NOT NULL,
    valeur        INT NOT NULL,
    PRIMARY KEY (idUtilisateur, idTouite),
    CONSTRAINT fk_note_utilisateur FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur (idUtilisateur),
    CONSTRAINT fk_note_touite      FOREIGN KEY (idTouite)      REFERENCES Touite      (idTouite)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Note (idUtilisateur, idTouite, valeur) VALUES
(2, 1, 1),
(3, 1, 1),
(1, 3, 1),
(2, 3, -1),
(3, 4, 1);

-- ------------------------------------------------------------
--  Images des touites (fonctionnalite 13)
-- ------------------------------------------------------------

CREATE TABLE Image (
    idImage     INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255) NULL,
    chemin      VARCHAR(255) NOT NULL,
    idTouite    INT          NOT NULL UNIQUE,
    CONSTRAINT fk_image_touite FOREIGN KEY (idTouite) REFERENCES Touite (idTouite)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  Triggers metier
-- ------------------------------------------------------------

DELIMITER $$

-- Incremente le compteur nbTouites de l'auteur a chaque publication
CREATE TRIGGER trg_ajouter_touite AFTER INSERT ON Touite FOR EACH ROW BEGIN
    UPDATE Utilisateur
    SET nbTouites = nbTouites + 1
    WHERE idUtilisateur = NEW.idAuteur;
END$$

-- Interdit de modifier l'auteur d'un touite
CREATE TRIGGER trg_bloquer_changement_touite BEFORE UPDATE ON Touite FOR EACH ROW BEGIN
    IF NEW.idAuteur <> OLD.idAuteur THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Impossible de modifier l''auteur d''un touite.';
    END IF;
END$$

-- Limite de 50 touites par jour et par auteur
CREATE TRIGGER trg_limite_50_touite BEFORE INSERT ON Touite FOR EACH ROW BEGIN
    DECLARE v_total INT;

    SELECT COUNT(*) INTO v_total
    FROM Touite
    WHERE idAuteur = NEW.idAuteur
      AND datePublication >= CURRENT_DATE()
      AND datePublication < CURRENT_DATE() + INTERVAL 1 DAY;

    IF v_total >= 50 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Limite de 50 touites par jour depassee.';
    END IF;
END$$

-- Interdit l'auto-suivi et le suivi d'un utilisateur au meme email
CREATE TRIGGER trg_verifier_suivre_insert BEFORE INSERT ON Suivre FOR EACH ROW BEGIN
    DECLARE v_email_suiveur VARCHAR(100);
    DECLARE v_email_suivi    VARCHAR(100);

    IF NEW.idSuiveur = NEW.idSuivi THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Un utilisateur ne peut pas se suivre lui-meme.';
    END IF;

    SELECT email INTO v_email_suiveur
    FROM Utilisateur
    WHERE idUtilisateur = NEW.idSuiveur;

    SELECT email INTO v_email_suivi
    FROM Utilisateur
    WHERE idUtilisateur = NEW.idSuivi;

    IF LOWER(TRIM(v_email_suiveur)) = LOWER(TRIM(v_email_suivi)) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Impossible de suivre un utilisateur ayant le meme email.';
    END IF;
END$$

-- Meme verification en cas de modification d'un lien de suivi
CREATE TRIGGER trg_verifier_suivre_update BEFORE UPDATE ON Suivre FOR EACH ROW BEGIN
    DECLARE v_email_suiveur VARCHAR(100);
    DECLARE v_email_suivi    VARCHAR(100);

    IF NEW.idSuiveur = NEW.idSuivi THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Un utilisateur ne peut pas se suivre lui-meme.';
    END IF;

    SELECT email INTO v_email_suiveur
    FROM Utilisateur
    WHERE idUtilisateur = NEW.idSuiveur;

    SELECT email INTO v_email_suivi
    FROM Utilisateur
    WHERE idUtilisateur = NEW.idSuivi;

    IF LOWER(TRIM(v_email_suiveur)) = LOWER(TRIM(v_email_suivi)) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Impossible de suivre un utilisateur ayant le meme email.';
    END IF;
END$$

DELIMITER ;
