<?php
declare(strict_types=1);

namespace iutnc\touiteur\render;

use iutnc\touiteur\domain\Touite;

class TweetDetailRenderer
{
    private Touite $touite;
    private ?string $imagePath;

    public function __construct(Touite $touite, ?string $imagePath = null)
    {
        $this->touite = $touite;
        $this->imagePath = $imagePath;
    }

    public function render(): string
    {
        $html = "<article class='tweet-detail'>";
        $redirect = htmlspecialchars("?action=tweet&id=" . $this->touite->getIdTouite());

        $html .= "<div class='detail-header'>";
        $html .= "<span class='tweet-author'>";
        $html .= "<a href='?action=user&amp;id=" . $this->touite->getIdAuteur() . "' class='author-link'>";
        $html .= "@" . htmlspecialchars($this->touite->getPseudoAuteur() ?? 'inconnu');
        $html .= "</a>";
        $html .= "</span>";
        $html .= "<form method='post' action='?action=suivre' style='display:contents'>";
        $html .= "<input type='hidden' name='idUtilisateur' value='" . $this->touite->getIdAuteur() . "'>";
        $html .= "<input type='hidden' name='redirect' value='" . $redirect . "'>";
        $html .= "<button type='submit' class='follow-button'>Suivre</button>";
        $html .= "</form>";
        $html .= "</div>";

        $html .= "<p class='detail-text'>";
        $html .= $this->rendreTexteAvecTags($this->touite->getTexte());
        $html .= "</p>";

        if ($this->imagePath !== null && $this->imagePath !== '') {
            $html .= "<div class='detail-image'>";
            $html .= "<img src='" . htmlspecialchars($this->imagePath, ENT_QUOTES, 'UTF-8') . "' alt='Image associée au touite'>";
            $html .= "</div>";
        }

        $html .= "<div class='detail-footer'>";
        $html .= "<div class='vote-controls'>";
        $html .= "<form method='post' action='?action=noter' style='display:contents'>";
        $html .= "<input type='hidden' name='idTouite' value='" . $this->touite->getIdTouite() . "'>";
        $html .= "<input type='hidden' name='valeur' value='1'>";
        $html .= "<input type='hidden' name='redirect' value='" . $redirect . "'>";
        $html .= "<button type='submit' class='vote-button' title='Upvote'>▲</button>";
        $html .= "</form>";
        $html .= "<span class='tweet-score'>" . $this->touite->getScore() . "</span>";
        $html .= "<form method='post' action='?action=noter' style='display:contents'>";
        $html .= "<input type='hidden' name='idTouite' value='" . $this->touite->getIdTouite() . "'>";
        $html .= "<input type='hidden' name='valeur' value='-1'>";
        $html .= "<input type='hidden' name='redirect' value='" . $redirect . "'>";
        $html .= "<button type='submit' class='vote-button' title='Downvote'>▼</button>";
        $html .= "</form>";
        $html .= "</div>";
        $html .= "<div class='tweet-date'>" . $this->formaterDate($this->touite->getDatePublication()) . "</div>";
        $html .= "</div>";

        $html .= "</article>";

        return $html;
    }

    private function formaterDate(string $date): string
    {
        $timestamp = strtotime($date);

        if ($timestamp === false) {
            return htmlspecialchars($date, ENT_QUOTES, 'UTF-8');
        }

        return date('d/m/y', $timestamp) . ' à ' . date('H:i', $timestamp);
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
