<?php
declare(strict_types=1);

namespace iutnc\touiteur\repository;

use PDO;
use iutnc\touiteur\domain\Touite;
use iutnc\touiteur\domain\Tag;

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
                       t.idAuteur, u.pseudo,
                       COALESCE(SUM(n.valeur), 0) AS score
                FROM Touite t
                INNER JOIN Utilisateur u
                    ON t.idAuteur = u.idUtilisateur
                LEFT JOIN Note n
                    On t.idTouite = n.idTouite
                GROUP BY t.idTouite, t.texte, t.datePublication, t.idAuteur, u.pseudo
                ORDER BY t.datePublication DESC";

        $stmt = $this->pdo->query($sql);
        $lignes = $stmt->fetchAll();

        $collection = [];

        foreach ($lignes as $ligne) {
            $collection[] = $this->creerTouiteDepuisLigne($ligne);
        }

        return $collection;
    }


    public function findTouitesByTag(string $libelle): array
    {
        $sql = "SELECT t.idTouite, t.texte, t.datePublication,
                   t.idAuteur, u.pseudo,
                   COALESCE(SUM(n.valeur), 0) AS score
            FROM Touite t
            INNER JOIN Touite_Tag tt ON t.idTouite = tt.idTouite
            INNER JOIN Tag tag ON tt.idTag = tag.idTag
            INNER JOIN Utilisateur u ON t.idAuteur = u.idUtilisateur
            LEFT JOIN Note n ON t.idTouite = n.idTouite
            WHERE tag.libelle = :libelle
            GROUP BY t.idTouite, t.texte, t.datePublication, t.idAuteur, u.pseudo
            ORDER BY t.datePublication DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'libelle' => $libelle
        ]);

        $lignes = $stmt->fetchAll();

        $collection = [];

        foreach ($lignes as $ligne) {
            $collection[] = $this->creerTouiteDepuisLigne($ligne);
        }

        return $collection;
    }

    public function findTouiteById(int $idTouite): ?Touite
    {
        $sql = "SELECT t.idTouite, t.texte, t.datePublication,
                       t.idAuteur, u.pseudo,
                       COALESCE(SUM(n.valeur), 0) AS score
                FROM Touite t
                INNER JOIN Utilisateur u
                    ON t.idAuteur = u.idUtilisateur
                LEFT JOIN Note n
                    ON t.idTouite = n.idTouite
                WHERE t.idTouite = :idTouite
                GROUP BY t.idTouite, t.texte, t.datePublication, t.idAuteur, u.pseudo
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idTouite' => $idTouite]);

        $ligne = $stmt->fetch();

        if ($ligne === false) {
            return null;
        }

        return $this->creerTouiteDepuisLigne($ligne);
    }

    public function findTouitesByAuteur(int $idAuteur): array
    {
        $sql = "SELECT t.idTouite, t.texte, t.datePublication,
                       t.idAuteur, u.pseudo,
                       COALESCE(SUM(n.valeur), 0) AS score
                FROM Touite t
                INNER JOIN Utilisateur u
                    ON t.idAuteur = u.idUtilisateur
                LEFT JOIN Note n
                    ON t.idTouite = n.idTouite
                WHERE t.idAuteur = :idAuteur
                GROUP BY t.idTouite, t.texte, t.datePublication, t.idAuteur, u.pseudo
                ORDER BY t.datePublication DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idAuteur' => $idAuteur]);

        $lignes = $stmt->fetchAll();
        $collection = [];

        foreach ($lignes as $ligne) {
            $collection[] = $this->creerTouiteDepuisLigne($ligne);
        }

        return $collection;
    }

    public function findHomeTouites(int $idUtilisateur): array
    {
        $sql = "SELECT t.idTouite, t.texte, t.datePublication,
                       t.idAuteur, u.pseudo,
                       COALESCE(SUM(n.valeur), 0) AS score
                FROM Touite t
                INNER JOIN Utilisateur u
                    ON t.idAuteur = u.idUtilisateur
                LEFT JOIN Note n
                    ON t.idTouite = n.idTouite
                WHERE EXISTS (
                    SELECT 1
                    FROM Suivre s
                    WHERE s.idSuiveur = :idSuiveur
                      AND s.idSuivi = t.idAuteur
                ) OR EXISTS (
                    SELECT 1
                    FROM Touite_Tag tt
                    INNER JOIN Tag tag ON tt.idTag = tag.idTag
                    INNER JOIN Abonnement_Tag at ON at.idTag = tag.idTag
                    WHERE tt.idTouite = t.idTouite
                      AND at.idUtilisateur = :idAbonne
                )
                GROUP BY t.idTouite, t.texte, t.datePublication, t.idAuteur, u.pseudo
                ORDER BY t.datePublication DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'idSuiveur' => $idUtilisateur,
            'idAbonne' => $idUtilisateur
        ]);

        $lignes = $stmt->fetchAll();
        $collection = [];

        foreach ($lignes as $ligne) {
            $collection[] = $this->creerTouiteDepuisLigne($ligne);
        }

        return $collection;
    }

    public function voter(int $idUtilisateur, int $idTouite, int $valeur): void
    {
        $sql = "INSERT INTO Note (idUtilisateur, idTouite, valeur)
                VALUES (:idUtilisateur, :idTouite, :valeur)
                ON DUPLICATE KEY UPDATE valeur = :valeurMaj";

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'idUtilisateur' => $idUtilisateur,
                    'idTouite' => $idTouite,
                    'valeur' => $valeur,
                    'valeurMaj' => $valeur
                ]);
    }

    private function creerTouiteDepuisLigne(array $ligne): Touite
    {
        $touite = new Touite(
            (int) $ligne['idTouite'],
            $ligne['texte'],
            $ligne['datePublication'],
            (int) $ligne['idAuteur'],
            $ligne['pseudo'],
            [],
            (int) $ligne['score']
        );

        $this->hydraterTags($touite);

        return $touite;
    }

    private function hydraterTags(Touite $touite): void
    {
        $sql = "SELECT tag.idTag, tag.libelle, tag.description
                FROM Tag tag
                INNER JOIN Touite_Tag tt ON tag.idTag = tt.idTag
                WHERE tt.idTouite = :idTouite";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idTouite' => $touite->getIdTouite()]);
        $lignes = $stmt->fetchAll();

        foreach ($lignes as $ligne) {
            $tag = new Tag(
                (int) $ligne['idTag'],
                $ligne['libelle'],
                $ligne['description'] ?? null
            );
            $touite->addTag($tag);
        }
    }
}


