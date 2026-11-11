<?php
declare(strict_types=1);

namespace iutnc\touiteur\traitement;

use iutnc\touiteur\repository\UtilisateurRepository;

class SuivreUtilisateur
{
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(UtilisateurRepository $utilisateurRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
    }

    public function execute(mixed $idUtilisateurASuivre): bool
    {

        $idSuiveur = (int) ($_SESSION['idUtilisateur'] ?? $_SESSION['user_id'] ?? 1);

        if ($idSuiveur <= 0) {
            throw new \Exception("Vous devez être connecté pour suivre un utilisateur.");
        }

        if ($idUtilisateurASuivre === null || (int) $idUtilisateurASuivre <= 0) {
            throw new \Exception("Utilisateur introuvable.");
        }

        $idUtilisateurASuivre = (int) $idUtilisateurASuivre;

        if ($idSuiveur === $idUtilisateurASuivre) {
            throw new \Exception("Vous ne pouvez pas vous suivre vous-même.");
        }

        $this->utilisateurRepository->suivreUtilisateur(
            $idSuiveur,
            $idUtilisateurASuivre
        );

        return true;
    }
}

