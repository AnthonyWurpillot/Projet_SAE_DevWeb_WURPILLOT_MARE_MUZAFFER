<?php
declare(strict_types=1);

require_once 'C:/xampp/htdocs/Projet_SAE_DevWeb_WURPILLOT_MARE_MUZAFFER/vendor/autoload.php';

use iutnc\touiteur\repository\Database;
use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\action\TagAction;

try {
    echo "<link rel='stylesheet' href='../public/css/style.css'>";
    Database::setConfig('C:/xampp/htdocs/Projet_SAE_DevWeb_WURPILLOT_MARE_MUZAFFER/config/db.config.ini');

    $repository = new TouiteRepository();

    // Tag que l'on souhaite afficher
    $_GET['tag'] = 'Java';

    // Création et exécution de l'action
    $action = new TagAction($repository);

    // Affichage du HTML généré par TagAction
    echo $action->execute();

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
