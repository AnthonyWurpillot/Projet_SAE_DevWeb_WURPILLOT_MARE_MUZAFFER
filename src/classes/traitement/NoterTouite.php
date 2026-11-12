<?php

declare(strict_types=1);

namespace iutnc\touiteur\traitement;

use iutnc\touiteur\repository\TouiteRepository;

class NoterTouite
{
    private TouiteRepository $touiteRepo;

    public  function __construct(TouiteRepository $touiteRepo)
    {
        $this->touiteRepo = $touiteRepo;
    }

    public function execute(int $idTouite, int $idUser, int $valeur): void
    {
        if ($idTouite <= 0 || $idUser <= 0) {
            throw new \InvalidArgumentException("Identifiant de touite ou d'utilisateur invalide.");

        }

        if (!in_array($valeur, [1, -1], true)) {
            throw new \InvalidArgumentException("Valeur de note non autorisée.");

        }

        $this->touiteRepo->voter($idUser, $idTouite, $valeur);

        }
    }



