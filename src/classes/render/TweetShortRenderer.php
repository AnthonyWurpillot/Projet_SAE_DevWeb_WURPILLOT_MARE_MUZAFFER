<?php
declare(strict_types=1);

namespace iutnc\touiteur\render;

use iutnc\touiteur\domain\Touite;

class TweetShortRenderer
{
    private Touite $touite;

    public function __construct(Touite $touite)
    {
        $this->touite = $touite;
    }

    public function render(): string
    {
        $html = "<article class='tweet'>";

        $html .= "<div class='tweet-header'>";
        $html .= "<span class='tweet-author'>";
        $html .= "@" . htmlspecialchars($this->touite->getPseudoAuteur() ?? 'inconnu');
        $html .= "</span>";
        $html .= "</div>";

        $html .= "<p class='tweet-text'>";
        $html .= htmlspecialchars($this->touite->getTexte());
        $html .= "</p>";

        $html .= "<div class='tweet-footer'>";
        $html .= "<a href='?action=tweet&id=" . $this->touite->getIdTouite() . "' class='view-tweet'>";
        $html .= "Voir le touite";
        $html .= "</a>";
        $html .= "</div>";

        $html .= "</article>";

        return $html;
    }
}
