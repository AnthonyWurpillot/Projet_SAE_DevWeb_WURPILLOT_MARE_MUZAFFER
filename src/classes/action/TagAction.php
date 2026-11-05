<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\render\TweetShortRenderer;

class TagAction extends Action
{
    private TouiteRepository $touiteRepository;

    public function __construct(TouiteRepository $touiteRepository)
    {
        $this->touiteRepository = $touiteRepository;
    }

    public function execute(): string
    {
        $libelle = isset($_GET['tag']) && is_string($_GET['tag'])
            ? trim($_GET['tag'])
            : '';

        $libelle = preg_replace('/^#+/', '', $libelle) ?? '';

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
            $html .= (new TweetShortRenderer($touite))->render();
        }

        if ($touites === []) {
            $html .= "<p>Aucun touite pour ce tag.</p>";
        }

        $html .= "</section>";

        return $html;
    }
}
