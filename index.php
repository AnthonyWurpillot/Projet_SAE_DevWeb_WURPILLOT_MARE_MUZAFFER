<?php
declare(strict_types=1);

session_start();

require_once 'vendor/autoload.php';

use iutnc\touiteur\repository\Database;
use iutnc\touiteur\dispatch\Dispatcher;

try {

    Database::setConfig('config/db.config.ini');

    $dispatcher = new Dispatcher();

    echo "<!DOCTYPE html>";
    echo "<html lang='fr'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>Touiteur</title>";
    echo "<link rel='stylesheet' href='public/css/style.css'>";
    echo "</head>";

    echo "<body>";

    echo $dispatcher->run();

    echo "</body>";
    echo "</html>";

} catch (Exception $e) {

    echo "<h1>Une erreur est survenue</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";

}
