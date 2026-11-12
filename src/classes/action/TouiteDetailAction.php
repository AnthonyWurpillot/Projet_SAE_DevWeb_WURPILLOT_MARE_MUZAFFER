<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\render\TweetDetailRenderer;

class TouiteDetailAction extends Action
{
    private TouiteRepository $touiteRepository;

    public function __construct(TouiteRepository $touiteRepository)
    {
        $this->touiteRepository = $touiteRepository;
    }

    public function execute(): string
    {
        $id = isset($_GET['id']) && is_string($_GET['id'])
            ? (int) $_GET['id']
            : 0;

        if ($id <= 0) {
            throw new \Exception("Touite invalide.");
        }

        $touite = $this->touiteRepository->findTouiteById($id);

        if ($touite === null) {
            throw new \Exception("Touite introuvable.");
        }

        $html = "<header class='main-header'>";
        $html .= "<h1>Touite</h1>";
        $html .= "</header>";

        $image = $this->touiteRepository->trouverImage($id);

        $html .= (new TweetDetailRenderer($touite, $image))->render();

        return $html;
    }
}
