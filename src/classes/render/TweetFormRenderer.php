<?php
declare(strict_types=1);

namespace iutnc\touiteur\render;

class TweetFormRenderer
{
    private ?string $redirect;

    public function __construct(?string $redirect = null)
    {
        $this->redirect = $redirect;
    }

    public function render(): string
    {
        $html = "<section class='tweet-composer'>";
        $html .= "<form method='post' action='?action=publier' enctype='multipart/form-data'>";

        $html .= "<textarea name='text' placeholder='Quoi de neuf ?' maxlength='235' rows='3' required></textarea>";

        if ($this->redirect !== null && $this->redirect !== '') {
            $html .= "<input type='hidden' name='redirect' value='" . htmlspecialchars($this->redirect) . "'>";
        }

        $html .= "<div id='image-preview' class='image-preview' style='display:none;'>";
        $html .= "<img id='preview-img' src='' alt='Aperçu de l image'>";
        $html .= "<div class='preview-info'>";
        $html .= "<span id='image-name'></span>";
        $html .= "<button type='button' id='remove-image'>Retirer l'image</button>";
        $html .= "</div>";
        $html .= "</div>";

        $html .= "<div class='composer-actions'>";
        $html .= "<label for='tweet-image' class='attachment-button'>+ Ajouter une image</label>";
        $html .= "<input type='file' id='tweet-image' name='image' accept='image/jpeg,image/png,image/gif,image/webp' hidden>";
        $html .= "<button type='submit' class='publish-button'>Publier</button>";
        $html .= "</div>";

        $html .= "</form>";
        $html .= "</section>";

        $html .= "
        <script>
        const inputImage = document.getElementById('tweet-image');
        const apercu = document.getElementById('image-preview');
        const image = document.getElementById('preview-img');
        const nomImage = document.getElementById('image-name');
        const boutonRetirer = document.getElementById('remove-image');

        let cheminTemporaire = null;

        inputImage.addEventListener('change', function() {
            const fichier = inputImage.files[0];

            if (!fichier) {
                return;
            }

            if (cheminTemporaire !== null) {
                URL.revokeObjectURL(cheminTemporaire);
            }

            cheminTemporaire = URL.createObjectURL(fichier);

            image.src = cheminTemporaire;
            nomImage.textContent = fichier.name;
            apercu.style.display = 'block';
        });

        boutonRetirer.addEventListener('click', function() {
            inputImage.value = '';
            image.src = '';
            nomImage.textContent = '';
            apercu.style.display = 'none';

            if (cheminTemporaire !== null) {
                URL.revokeObjectURL(cheminTemporaire);
                cheminTemporaire = null;
            }
        });
        </script>";

        return $html;
    }
}
