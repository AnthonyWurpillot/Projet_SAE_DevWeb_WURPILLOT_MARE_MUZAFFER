<?php
declare(strict_types=1);

namespace iutnc\touiteur\render;

class TweetFormRenderer
{
    public function render(): string
    {
        $html = "<section class='tweet-composer'>";
        $html .= "<form method='post' action='?action=publier' enctype='multipart/form-data'>";
        $html .= "<textarea name='text' placeholder='Quoi de neuf ?' maxlength='235' rows='3' required></textarea>";
        $html .= "<div class='composer-actions'>";
        $html .= "<label for='tweet-image' class='attachment-button'>Ajouter une image</label>";
        $html .= "<input type='file' id='tweet-image' name='image' accept='image/*' hidden>";
        $html .= "<button type='submit' class='publish-button'>Publier</button>";
        $html .= "</div>";
        $html .= "</form>";
        $html .= "</section>";

        return $html;
    }
}
