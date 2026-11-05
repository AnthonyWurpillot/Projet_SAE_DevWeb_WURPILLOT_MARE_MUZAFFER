<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\UtilisateurRepository;
use iutnc\touiteur\render\InfluenceurRenderer;

class InfluenceursAction extends Action
{
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(UtilisateurRepository $utilisateurRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
    }

    public function execute(): string
    {
        $influenceurs = $this->utilisateurRepository->findInfluenceurs();

        $html = "<header class='main-header'>";
        $html .= "<h1>Influenceurs</h1>";
        $html .= "</header>";

        $html .= "<section class='tweet-feed'>";

        foreach ($influenceurs as $influenceur) {
            $html .= (new InfluenceurRenderer(
                $influenceur['utilisateur'],
                $influenceur['nbSuiveurs']
            ))->render();
        }

        if ($influenceurs === []) {
            $html .= "<p>Aucun influenceur pour le moment.</p>";
        }

        $html .= "</section>";

        return $html;
    }
}
