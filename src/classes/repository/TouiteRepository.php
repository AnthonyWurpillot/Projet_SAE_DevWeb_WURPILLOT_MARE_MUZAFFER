<?php
declare(strict_types=1);

namespace iutnc\touiteur\repository;

use PDO;
use iutnc\touiteur\domain\Touite;

class TouiteRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getPDO();
    }

    public function create(string $texte, int $idAuteur): int
    {
        $sql = "INSERT INTO Touite (texte, idAuteur)
                VALUES (:texte, :idAuteur)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'texte' => $texte,
            'idAuteur' => $idAuteur
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findAllTouites(): array
    {
        $sql = "SELECT t.idTouite, t.texte, t.datePublication,
                       t.idAuteur, u.pseudo
                FROM Touite t
                INNER JOIN Utilisateur u
                    ON t.idAuteur = u.idUtilisateur
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
