<?php
declare(strict_types=1);

namespace iutnc\touiteur\traitement;

use iutnc\touiteur\repository\TagRepository;

class AbonnerTag
{
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function execute(mixed $libelle): bool
    {

        $idUtilisateur = (int) ($_SESSION['idUtilisateur'] ?? 0);

        if ($idUtilisateur <= 0) {
            throw new \Exception("Vous devez être connecté pour vous abonner à un tag.");
        }

        if ($libelle === null || trim($libelle) === '') {
            throw new \Exception("Aucun tag sélectionné.");
        }

        $nomTag = ltrim(trim((string) $libelle), '#');
        $idTag = $this->tagRepository->getTagIdByNom($nomTag);

        if ($idTag === null) {
            throw new \Exception("Ce tag n'existe pas");
        }

        // Vérifier si l'utilisateur est déjà abonné
        if ($this->tagRepository->estAbonneAuTag($idUtilisateur, $idTag)) {
            throw new \Exception("Vous êtes déjà abonné à ce tag.");
        }

        $this->tagRepository->abonnerTag($idUtilisateur, $idTag);

        return true;
    }
}


