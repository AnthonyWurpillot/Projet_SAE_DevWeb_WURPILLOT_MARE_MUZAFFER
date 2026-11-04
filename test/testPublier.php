<?php
declare(strict_types=1);

session_start();

require_once 'C:/xampp/htdocs/Projet_SAE_DevWeb_WURPILLOT_MARE_MUZAFFER/vendor/autoload.php';

use iutnc\touiteur\repository\Database;
use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\repository\TagRepository;
use iutnc\touiteur\traitement\PublierTouite;

try {
    Database::setConfig(
        'C:/xampp/htdocs/Projet_SAE_DevWeb_WURPILLOT_MARE_MUZAFFER/config/db.config.ini'
    );

    $touiteRepository = new TouiteRepository();
    $tagRepository = new TagRepository();

    $_SESSION['idUtilisateur'] = 1;

    $publierTouite = new PublierTouite(
        $touiteRepository,
        $tagRepository
    );

    $idTouite = $publierTouite->execute(
        "Je découvre #Java et #PHP pour ma #SAE !"
    );

    echo "Touite publié avec succès !<br>";
    echo "Identifiant du touite : " . $idTouite;

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
