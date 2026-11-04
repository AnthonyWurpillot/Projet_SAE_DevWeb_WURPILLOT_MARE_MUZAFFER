<?php
declare(strict_types=1);

session_start();

require_once 'C:/xampp/htdocs/Projet_SAE_DevWeb_WURPILLOT_MARE_MUZAFFER/vendor/autoload.php';

use iutnc\touiteur\repository\Database;
use iutnc\touiteur\repository\UtilisateurRepository;
use iutnc\touiteur\traitement\SuivreUtilisateur;

try {
    Database::setConfig(
        'C:/xampp/htdocs/Projet_SAE_DevWeb_WURPILLOT_MARE_MUZAFFER/config/db.config.ini'
    );

    $utilisateurRepository = new UtilisateurRepository();

    $_SESSION['idUtilisateur'] = 1;

    $suivreUtilisateur = new SuivreUtilisateur(
        $utilisateurRepository
    );

    $suivreUtilisateur->execute(2);

    echo "Utilisateur suivi avec succès !";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}