<?php
declare(strict_types=1);

session_start();

require_once 'vendor/autoload.php';

use iutnc\touiteur\repository\TouiteRepository;

try {
TouiteRepository::setConfig('config/db.config.ini');
$repo = TouiteRepository::getInstance();
$pdo = $repo->getPDO();

echo "<h1 style='color:green;'>Connexion réussie à la base de données MySQL via TouiteRepository !</h1>";
} catch (\Exception $e) {
echo "<h1 style='color:red;'>Erreur de connexion !</h1>";
echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}