<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\action\ActionPublierTouite;

try {
    // 1. Charger la configuration
    TouiteRepository::setConfig(__DIR__ . '/config/db.config.ini');

    // 2. Récupérer le repository
    $repository = TouiteRepository::getInstance();

    // 3. Simuler un utilisateur connecté
    $_SESSION['idUtilisateur'] = 1;

    // 4. Créer l'action
    $action = new ActionPublierTouite($repository);

    // 5. Exécuter l'action
    $idTouite = $action->execute("Mon premier touite de test !");

    echo "Touite publié avec succès !<br>";
    echo "Identifiant du touite : " . $idTouite;

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}