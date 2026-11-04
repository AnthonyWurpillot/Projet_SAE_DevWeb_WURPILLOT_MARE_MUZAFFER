<?php
declare(strict_types=1);

namespace iutnc\touiteur\traitement;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\repository\TagRepository;

class PublierTouite
{
    private TouiteRepository $touiteRepository;
    private TagRepository $tagRepository;

    public function __construct(
        TouiteRepository $touiteRepository,
        TagRepository $tagRepository
    ) {
        $this->touiteRepository = $touiteRepository;
        $this->tagRepository = $tagRepository;
    }

    public function execute($texte)
    {
        $texte = trim($texte);

        if ($texte === "") {
            throw new \Exception("Le touite ne peut pas être vide.");
        }

        if (mb_strlen($texte) > 235) {
            throw new \Exception("Le touite ne peut pas dépasser 235 caractères.");
        }

        if (!isset($_SESSION['idUtilisateur'])) {
            throw new \Exception("Vous devez être connecté pour publier.");
        }

        $idAuteur = (int) $_SESSION['idUtilisateur'];

        // Extraction des hashtags
        $tags = explode(" ", $texte);

        foreach ($tags as $i => $mot) {
            if (str_starts_with($mot, "#")) {
                $tags[$i] = substr($mot, 1);
            } else {
                unset($tags[$i]);
            }
        }

        $tags = array_unique($tags);

        // Création du touite
        $idTouite = $this->touiteRepository->create($texte, $idAuteur);

        // Enregistrement des hashtags
        foreach ($tags as $libelle) {

            $idTag = $this->tagRepository->getTagByNom($libelle);

            if ($idTag === null) {
                $idTag = $this->tagRepository->createTag($libelle);
            }

            $this->tagRepository->associerTagAuTouite(
                $idTouite,
                $idTag
            );
        }

        return $idTouite;
    }
}
