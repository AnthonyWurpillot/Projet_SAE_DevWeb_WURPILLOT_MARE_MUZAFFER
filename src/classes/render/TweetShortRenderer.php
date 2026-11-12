<?php
declare(strict_types=1);

namespace iutnc\touiteur\render;

use iutnc\touiteur\domain\Touite;

class TweetShortRenderer
{
    private Touite $touite;
    private ?string $image;

    public function __construct(Touite $touite, ?string $image = null)
    {
        $this->touite = $touite;
        $this->image = $image;
    }

    public function render(): string
    {
        $html = "<article class='tweet'>";

        $html .= "<div class='tweet-header'>";
        $html .= "<span class='tweet-author'>";
        $html .= "<a href='?action=user&amp;id=" . $this->touite->getIdAuteur() . "' class='author-link'>";
        $html .= "@" . htmlspecialchars($this->touite->getPseudoAuteur() ?? 'inconnu');
        $html .= "</a>";
        $html .= "</span>";
        $html .= "</div>";

        $html .= "<p class='tweet-text'>";
        $html .= $this->rendreTexteAvecTags($this->touite->getTexte());
        $html .= "</p>";
        if ($this->image !== null && $this->image !== '') {
            $html .= "<div class='tweet-image'>";
            $html .= "<img src='" . htmlspecialchars($this->image, ENT_QUOTES, 'UTF-8') . "' alt='Image du touite'>";
            $html .= "</div>";
        }

        $html .= "<div class='tweet-footer'>";
        $html .= "<a href='?action=tweet&amp;id=" . $this->touite->getIdTouite() . "' class='view-tweet'>";
        $html .= "Voir le touite";
        $html .= "</a>";
        $html .= "</div>";

        $html .= "</article>";

        return $html;
    }

    private function rendreTexteAvecTags(string $texte): string
    {
        $texteHtml = htmlspecialchars($texte, ENT_QUOTES, 'UTF-8');
        $tags = $this->touite->getTags();

        foreach ($tags as $tag) {
            $libelle = $tag->getLibelle();
            $libelleHtml = htmlspecialchars($libelle, ENT_QUOTES, 'UTF-8');
            $pattern = '/#' . preg_quote($libelleHtml, '/') . '(?![\p{L}\p{N}_])/u';
            $lien = "<a href='?action=tag&amp;tag=" . urlencode($libelle) . "' class='tag-link'>#" . $libelleHtml . "</a>";

            $texteHtml = preg_replace_callback($pattern, fn() => $lien, $texteHtml) ?? $texteHtml;
        }

        return $texteHtml;
    }
}
