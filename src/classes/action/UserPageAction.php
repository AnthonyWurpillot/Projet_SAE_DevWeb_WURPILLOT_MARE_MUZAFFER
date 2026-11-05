<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\repository\UtilisateurRepository;
use iutnc\touiteur\render\TweetShortRenderer;

class UserPageAction extends Action
{
    private UtilisateurRepository $utilisateurRepository;
    private TouiteRepository $touiteRepository;

    public function __construct(
        UtilisateurRepository $utilisateurRepository,
        TouiteRepository $touiteRepository
    ) {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->touiteRepository = $touiteRepository;
    }

    public function execute(): string
    {
        $id = isset($_GET['id']) && is_string($_GET['id'])
            ? (int) $_GET['id']
            : 0;

        if ($id <= 0) {
            throw new \Exception("Utilisateur invalide.");
        }

        $utilisateur = $this->utilisateurRepository->findUtilisateurById($id);

        if ($utilisateur === null) {
            throw new \Exception("Utilisateur introuvable.");
        }

        $touites = $this->touiteRepository->findTouitesByAuteur($id);

        $html = "<header class='main-header'>";
        $html .= "<h1>Profil utilisateur</h1>";
        $html .= "</header>";

        $html .= "<section class='user-profile'>";
        $html .= "<div class='user-profile-header'>";
        $html .= "<h2>@" . htmlspecialchars($utilisateur->getPseudo()) . "</h2>";
        $html .= "<form method='post' action='?action=suivre' style='display:contents'>";
        $html .= "<input type='hidden' name='idUtilisateur' value='" . $utilisateur->getIdUtilisateur() . "'>";
        $html .= "<button type='submit' class='follow-button'>Suivre</button>";
        $html .= "</form>";
        $html .= "</div>";
        $html .= "<p>" . $utilisateur->getNbTouites() . " touite(s)</p>";
        $html .= "</section>";

        $html .= "<section class='tweet-feed'>";

        foreach ($touites as $touite) {
            $html .= (new TweetShortRenderer($touite))->render();
        }

        if ($touites === []) {
            $html .= "<p>Aucun touite pour cet utilisateur.</p>";
        }

        $html .= "</section>";

        return $html;
    }
}
