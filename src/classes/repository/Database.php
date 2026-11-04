<?php
declare(strict_types=1);

namespace iutnc\touiteur\repository;

use PDO;
use PDOException;
use Exception;

class Database
{
    private static ?PDO $pdo = null;
    private static array $config = [];

    private function __construct() {}

    public static function setConfig(string $file): void
    {
        $conf = parse_ini_file($file);

        if ($conf === false) {
            throw new Exception("Impossible de lire le fichier de configuration.");
        }

        self::$config = $conf;
    }

    public static function getPDO(): PDO
    {
        if (self::$pdo === null) {

            if (empty(self::$config)) {
                throw new Exception("Configuration non initialisée.");
            }

            $driver = self::$config["driver"] ?? 'mysql';
            $host = self::$config["host"] ?? 'localhost';
            $database = self::$config["database"] ?? 'touiteur';
            $username = self::$config["username"] ?? 'root';
            $password = self::$config["password"] ?? '';

            $dsn = "{$driver}:host={$host};dbname={$database};charset=utf8mb4";

            try {
                self::$pdo = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                throw new Exception("Erreur de connexion PDO : " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
