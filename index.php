<?php
declare(strict_types=1);

session_start();

require_once 'vendor/autoload.php';

use iutnc\touiteur\repository\Database;
use iutnc\touiteur\dispatch\Dispatcher;

try {
    Database::setConfig('config/db.config.ini');
    (new Dispatcher())->run();

} catch (Throwable $e) {
    echo "<h1>Une erreur est survenue</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
