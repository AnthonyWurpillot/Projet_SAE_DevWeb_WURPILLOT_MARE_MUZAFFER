<?php
declare(strict_types=1);

namespace iutnc\touiteur\repository;

use PDO;
use PDOException;
use Exception;
use iutnc\touiteur\domain\Touite;

class TouiteRepository {
    private static ?TouiteRepository $instance = null;
    private static array $config = [];
    private ?PDO $pdo = null;

    private function __construct() {
        if (empty(self::$config)) {
            throw new Exception("Configuration de la base de donnée non initialisée.");
        }

        $driver = self::$config["driver"] ?? 'mysql';
        $host = self::$config["host"] ?? 'localhost';
        $database = self::$config["database"] ?? 'touiteur';
        $username = self::$config["username"] ?? 'root';
        $password = self::$config["password"] ?? '';

        $dsn = "{$driver}:host={$host};dbname={$database};charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur de connexion PDO : " . $e->getMessage());
        }
    }

    public static function setConfig(string $file): void {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new Exception("Impossible de lire le fichier de configuration : {$file}");
        }
        self::$config = $conf;
    }

    public static function getInstance(): TouiteRepository {
        if (is_null(self::$instance)) {
            self::$instance = new TouiteRepository();
        }
        return self::$instance;
    }

    public function getPDO(): PDO {
        return $this->pdo;
    }

    public function create(string $texte, int $idAuteur): int {
        $sql = "INSERT INTO Touite (texte, idAuteur)
                VALUES (:texte, :idAuteur)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'texte' => $texte,
            'idAuteur' => $idAuteur
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function suivreUtilisateur(int $idSuiveur, int $idSuivi): void {
        $sql = "INSERT INTO Suivre (idSuiveur, idSuivi)
                VALUES (:idSuiveur, :idSuivi)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'idSuiveur' => $idSuiveur,
            'idSuivi' => $idSuivi
        ]);
    }

    public function findAllTouites(): array {
        $sql = "SELECT t.idTouite, t.texte, t.datePublication, t.idAuteur, u.pseudo
                FROM Touite t
                INNER JOIN Utilisateur u ON t.idAuteur = u.idUtilisateur
                ORDER BY t.datePublication DESC";

        $stmt = $this->pdo->query($sql);
        $lignes = $stmt->fetchAll();

        $collection = [];
        foreach ($lignes as $ligne) {
            $collection[] = new Touite(
                (int) $ligne['idTouite'],
                $ligne['texte'],
                $ligne['datePublication'],
                (int) $ligne['idAuteur'],
                $ligne['pseudo']
            );
        }
        return $collection;
    }
}