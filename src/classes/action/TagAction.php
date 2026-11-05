<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\render\TweetShortRenderer;

class TagAction
{
    private TouiteRepository $touiteRepository;

    public function __construct(TouiteRepository $touiteRepository)
    {
        $this->touiteRepository = $touiteRepository;
    }

    public function execute(): string
    {
        if (isset($_GET['tag'])) {
            $libelle = $_GET['tag'];
        } else {
            $libelle = '';
        }

        if ($libelle === '') {
            throw new \Exception("Aucun tag sélectionné.");
        }

        $touites = $this->touiteRepository->findTouitesByTag($libelle);

        $html = "<header class='main-header'>";
        $html .= "<h1>Publications associées au tag</h1>";
        $html .= "</header>";

        $html .= "<section class='tag-header'>";
        $html .= "<h2>#" . htmlspecialchars($libelle) . "</h2>";
        $html .= "<p>Publications contenant ce tag</p>";
        $html .= "</section>";

        $html .= "<section class='tweet-feed'>";

        foreach ($touites as $touite) {
            $renderer = new TweetShortRenderer($touite);
            $html .= $renderer->render();
        }

        $html .= "</section>";

        return $html;
    }
}