<?php
declare(strict_types=1);

namespace iutnc\touiteur\repository;

use PDO;

class TagRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getPDO();
    }

    public function getTagByNom(string $libelle): ?int
    {
        $sql = "SELECT idTag
                FROM Tag
                WHERE libelle = :libelle";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'libelle' => $libelle
        ]);

        $idTag = $stmt->fetchColumn();

        return $idTag !== false ? (int) $idTag : null;
    }

    public function createTag(string $libelle): int
    {
        $sql = "INSERT INTO Tag (libelle)
                VALUES (:libelle)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'libelle' => $libelle
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function associerTagAuTouite(int $idTouite, int $idTag): void
    {
        $sql = "INSERT INTO Touite_Tag (idTouite, idTag)
                VALUES (:idTouite, :idTag)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'idTouite' => $idTouite,
            'idTag' => $idTag
        ]);
    }
}
