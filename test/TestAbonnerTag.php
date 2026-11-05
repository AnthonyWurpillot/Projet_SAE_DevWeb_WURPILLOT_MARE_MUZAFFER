<?php
declare(strict_types=1);

require_once 'C:/xampp/htdocs/Projet_SAE_DevWeb_WURPILLOT_MARE_MUZAFFER/vendor/autoload.php';

use iutnc\touiteur\repository\Database;
use iutnc\touiteur\repository\TagRepository;
use iutnc\touiteur\traitement\AbonnerTag;

try {
    Database::setConfig('C:/xampp/htdocs/Projet_SAE_DevWeb_WURPILLOT_MARE_MUZAFFER/config/db.config.ini');

    session_start();

    $_SESSION['idUtilisateur'] = 1;

    $repository = new TagRepository();

    // Création du traitement
    $traitement = new AbonnerTag($repository);

    $traitement->execute('Java');

    echo "Abonnement au tag Java effectué avec succès !";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
