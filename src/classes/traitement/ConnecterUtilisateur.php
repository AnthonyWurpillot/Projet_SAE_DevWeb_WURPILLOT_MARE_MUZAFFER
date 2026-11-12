<?php
declare(strict_types=1);

namespace iutnc\touiteur\traitement;

use iutnc\touiteur\repository\UtilisateurRepository;

class ConnecterUtilisateur
{
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(UtilisateurRepository $utilisateurRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
    }

    public function execute(mixed $idUtilisateur): void
    {
        $id = (int) $idUtilisateur;

        if ($id <= 0) {
            throw new \Exception("Utilisateur invalide.");
        }

        $utilisateur = $this->utilisateurRepository->findUtilisateurById($id);

        if ($utilisateur === null) {
            throw new \Exception("Utilisateur introuvable.");
        }

        $_SESSION['idUtilisateur'] = $utilisateur->getIdUtilisateur();
    }
}
