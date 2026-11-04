<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\action\ActionSuivreUtilisateur;

try {
    // 1. Charger la configuration
    TouiteRepository::setConfig(__DIR__ . '/config/db.config.ini');

    // 2. Récupérer le repository
    $repository = TouiteRepository::getInstance();

    // 3. Simuler un utilisateur connecté (Mouzaffer)
    $_SESSION['idUtilisateur'] = 2;

    // 4. Créer l'action
    $action = new ActionSuivreUtilisateur($repository);

    // 5. Faire suivre l'utilisateur 2 (Axel)
    $action->execute(1);

    echo "Utilisateur suivi avec succès !";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}