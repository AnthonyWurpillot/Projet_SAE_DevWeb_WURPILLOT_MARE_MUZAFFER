<?php

declare(strict_types=1);

namespace iutnc\touiteur\action;

class ActionSuivreUtilisateur
{
    private $touiteRepository;

    public function __construct($touiteRepository)
    {
        $this->touiteRepository = $touiteRepository;
    }

    public function execute($idUtilisateurASuivre)
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['idUtilisateur'])) {
            throw new \Exception("Vous devez être connecté pour suivre un utilisateur.");
        }

        // Récupérer l'identifiant de l'utilisateur connecté
        $idSuiveur = (int) $_SESSION['idUtilisateur'];

        // Vérifier l'identifiant de l'utilisateur à suivre
        if (
            $idUtilisateurASuivre === null ||
            filter_var($idUtilisateurASuivre, FILTER_VALIDATE_INT) === false ||
            (int) $idUtilisateurASuivre <= 0
        ) {
            throw new \Exception("Utilisateur introuvable.");
        }

        $idUtilisateurASuivre = (int) $idUtilisateurASuivre;

        // Empêcher de se suivre soi-même
        if ($idSuiveur === $idUtilisateurASuivre) {
            throw new \Exception("Vous ne pouvez pas vous suivre vous-même.");
        }

        // Enregistrer l'abonnement
        $this->touiteRepository->suivreUtilisateur(
            $idSuiveur,
            $idUtilisateurASuivre
        );

        return true;
    }
}
