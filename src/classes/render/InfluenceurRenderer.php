<?php
declare(strict_types=1);

namespace iutnc\touiteur\render;

use iutnc\touiteur\domain\Utilisateur;

class InfluenceurRenderer
{
    private Utilisateur $utilisateur;
    private int $nbSuiveurs;

    public function __construct(Utilisateur $utilisateur, int $nbSuiveurs)
    {
        $this->utilisateur = $utilisateur;
        $this->nbSuiveurs = $nbSuiveurs;
    }

    public function render(): string
    {
        $html = "<article class='tweet'>";

        $html .= "<div class='tweet-header'>";
        $html .= "<span class='tweet-author'>";
        $html .= "<a href='?action=user&amp;id=" . $this->utilisateur->getIdUtilisateur() . "' class='author-link'>";
        $html .= "@" . htmlspecialchars($this->utilisateur->getPseudo());
        $html .= "</a>";
        $html .= "</span>";
        $html .= "</div>";

        $html .= "<p class='tweet-text'>";
        $html .= $this->nbSuiveurs . " abonné(s)";
        $html .= "</p>";

        $html .= "<div class='tweet-footer'>";
        $html .= "<a href='?action=user&amp;id=" . $this->utilisateur->getIdUtilisateur() . "' class='view-tweet'>";
        $html .= "Voir le profil";
        $html .= "</a>";
        $html .= "</div>";

        $html .= "</article>";

        return $html;
    }
}
