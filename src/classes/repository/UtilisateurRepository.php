<?php
declare(strict_types=1);

namespace iutnc\touiteur\repository;

use PDO;

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
}
