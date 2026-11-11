<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\render\TweetFormRenderer;
use iutnc\touiteur\render\TweetShortRenderer;

class ExploreAction extends Action
{
    private TouiteRepository $touiteRepository;

    public function __construct(TouiteRepository $touiteRepository)
    {
        $this->touiteRepository = $touiteRepository;
    }

    public function execute(): string
    {
        $touites = $this->touiteRepository->findAllTouites();

        $html = "<header class='main-header'>";
        $html .= "<h1>Explorer</h1>";
        $html .= "</header>";

        $html .= (new TweetFormRenderer('?action=explore'))->render();

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
