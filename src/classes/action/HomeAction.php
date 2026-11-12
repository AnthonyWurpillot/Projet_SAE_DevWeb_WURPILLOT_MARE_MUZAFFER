<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\render\TweetFormRenderer;
use iutnc\touiteur\render\TweetShortRenderer;

class HomeAction extends Action
{
    private TouiteRepository $touiteRepository;

    public function __construct(TouiteRepository $touiteRepository)
    {
        $this->touiteRepository = $touiteRepository;
    }

    public function execute(): string
    {
        $idUtilisateur = isset($_SESSION['idUtilisateur'])
            ? (int) $_SESSION['idUtilisateur']
            : null;

        if ($idUtilisateur !== null) {
            $touites = $this->touiteRepository->findHomeTouites($idUtilisateur);
        } else {
            $touites = $this->touiteRepository->findAllTouites();
        }

        $html = "<header class='main-header'>";
        $html .= "<h1>Accueil</h1>";
        $html .= "</header>";

        $html .= (new TweetFormRenderer('?action=home'))->render();

        $html .= "<section class='tweet-feed'>";

        foreach ($touites as $touite) {
            $html .= (new TweetShortRenderer($touite))->render();
        }

        if ($touites === []) {
            $html .= "<p>Aucun touite pour le moment.</p>";
        }

        $html .= "</section>";

        return $html;
    }
}
