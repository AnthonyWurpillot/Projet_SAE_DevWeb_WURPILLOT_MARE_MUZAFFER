<?php
declare(strict_types=1);

namespace iutnc\touiteur\repository;

use PDO;
use iutnc\touiteur\domain\Utilisateur;

class UtilisateurRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getPDO();
    }

    public function suivreUtilisateur(int $idSuiveur, int $idSuivi): void
    {
        $sql = "INSERT INTO Suivre (idSuiveur, idSuivi)
                VALUES (:idSuiveur, :idSuivi)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'idSuiveur' => $idSuiveur,
            'idSuivi' => $idSuivi
        ]);
    }

    public function findUtilisateurById(int $idUtilisateur): ?Utilisateur
    {
        $sql = "SELECT idUtilisateur, pseudo, nom, prenom, email, nbTouites
                FROM Utilisateur
                WHERE idUtilisateur = :idUtilisateur
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idUtilisateur' => $idUtilisateur]);

        $ligne = $stmt->fetch();

        if ($ligne === false) {
            return null;
        }

        return $this->creerUtilisateurDepuisLigne($ligne);
    }

    public function findInfluenceurs(): array
    {
        $sql = "SELECT u.idUtilisateur, u.pseudo, u.nom, u.prenom, u.email, u.nbTouites,
                       COUNT(s.idSuivi) AS nbSuiveurs
                FROM Utilisateur u
                LEFT JOIN Suivre s ON s.idSuivi = u.idUtilisateur
                GROUP BY u.idUtilisateur, u.pseudo, u.nom, u.prenom, u.email, u.nbTouites
                ORDER BY nbSuiveurs DESC, u.pseudo ASC";

        $stmt = $this->pdo->query($sql);
        $lignes = $stmt->fetchAll();

        $collection = [];

        foreach ($lignes as $ligne) {
            $collection[] = [
                'utilisateur' => $this->creerUtilisateurDepuisLigne($ligne),
                'nbSuiveurs' => (int) $ligne['nbSuiveurs']
            ];
        }

        return $collection;
    }

    private function creerUtilisateurDepuisLigne(array $ligne): Utilisateur
    {
        return new Utilisateur(
            (int) $ligne['idUtilisateur'],
            $ligne['pseudo'],
            $ligne['nom'] ?? null,
            $ligne['prenom'] ?? null,
            $ligne['email'],
            (int) $ligne['nbTouites']
        );
    }
}
