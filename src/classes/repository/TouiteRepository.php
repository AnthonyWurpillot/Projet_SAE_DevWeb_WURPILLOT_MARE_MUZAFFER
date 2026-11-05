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
            $collection [] = $touite;
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
            $collection[] = $touite;
        }

        return $collection;
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

        foreach($lignes as $ligne) {
        $tag = new Tag(
        (int) $ligne['idTag'],
        $ligne['libelle'],
        $ligne['description'] ?? null
        );
        $touite->addTag($tag);

        }
}
}


