<?php

declare(strict_types=1);

namespace iutnc\touiteur\action;

class ActionPublierTouite
{
    private $touiteRepository;

    public function __construct($touiteRepository)
    {
        $this->touiteRepository = $touiteRepository;
    }

    public function execute($texte)
    {
        $texte = trim($texte);

        // Vérifier que le touite n'est pas vide
        if ($texte === "") {
            throw new \Exception("Le touite ne peut pas être vide.");
        }

        // Vérifier la longueur du touite
        if (mb_strlen($texte) > 235) {
            throw new \Exception("Le touite ne peut pas dépasser 235 caractères.");
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['idUtilisateur'])) {
            throw new \Exception("Vous devez être connecté pour publier.");
        }

        $idAuteur = $_SESSION['idUtilisateur'];

        // Extraire les hashtags
        $tags = explode(" ", $texte);

        foreach ($tags as $i => $mot) {
            if (str_starts_with($mot, "#")) {
                $tags[$i] = substr($mot, 1);
            } else {
                unset($tags[$i]);
            }
        }

        $tags = array_unique($tags);

        // Enregistrer le touite en base de données
        $idTouite = $this->touiteRepository->create($texte, $idAuteur);

        // Association des hashtags à implémenter plus tard

        return $idTouite;
    }
}
