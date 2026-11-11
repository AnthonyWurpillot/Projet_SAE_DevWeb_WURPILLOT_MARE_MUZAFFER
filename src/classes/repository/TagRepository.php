<?php
declare(strict_types=1);

namespace iutnc\touiteur\repository;

use PDO;
use iutnc\touiteur\db\Database;

class TagRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getPDO();
    }

    public function getTagIdByNom(string $libelle): ?int
    {
        $sql = "SELECT idTag FROM Tag WHERE libelle = :libelle";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['libelle' => $libelle]);

        $resultat = $stmt->fetch();

        if ($resultat === false) {
            return null;
        }

        return (int) $resultat['idTag'];
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
        $sql = "INSERT IGNORE INTO Touite_Tag (idTouite, idTag)
                VALUES (:idTouite, :idTag)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'idTouite' => $idTouite,
            'idTag' => $idTag
        ]);
    }
    public function abonnerTag(int $idUtilisateur, int $idTag): void
    {
        $sql = "INSERT IGNORE INTO Abonnement_Tag (idUtilisateur, idTag)
            VALUES (:idUtilisateur, :idTag)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'idUtilisateur' => $idUtilisateur,
            'idTag' => $idTag
        ]);
    }

public function estAbonneAuTag(int $idUtilisateur, int $idTag):  bool
{
    $sql = "SELECT 1 FROM Abonnement_Tag
            WHERE idUtilisateur = :idUtilisateur
            AND idTag = :idTag";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                 'idUtilisateur' => $idUtilisateur,
                 'idTag' => $idTag
            ]);

            return $stmt->fetch() !==false;
            }
        }
