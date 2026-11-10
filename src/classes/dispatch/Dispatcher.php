<?php
declare(strict_types=1);

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\repository\UtilisateurRepository;
use iutnc\touiteur\repository\TagRepository;

use iutnc\touiteur\action\HomeAction;
use iutnc\touiteur\action\ExploreAction;
use iutnc\touiteur\action\TouiteDetailAction;
use iutnc\touiteur\action\UserPageAction;
use iutnc\touiteur\action\InfluenceursAction;
use iutnc\touiteur\action\TagAction;

use iutnc\touiteur\traitement\PublierTouite;

class Dispatcher
{

    private ?string $action;
    private TouiteRepository $touiteRepo;
    private UtilisateurRepository $utilisateurRepo;
    private TagRepository $tagRepo;

    public function __construct()
    {

    $this->action = $_GET['action']?? 'home';
    $this->touiteRepo = new TouiteRepository();
    $this->utilisateurRepo = new UtilisateurRepository();
    $this->tagRepo = new TagRepository();

    }

    public function run(): void
    {

    try {

    switch ($this->action) {
        case 'publier':
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $publierService = new PublierTouite(
                    $this->touiteRepo,
                    $this->tagRepo
                );
                $publierService->execute($_POST['text'] ?? '');
            }

        header('Location: ?action=home');
        exit;

        case'explore':
        $actionObj = new ExploreAction($this->touiteRepo);
        break;

        case 'tweet':
        case 'detail':
        case 'detail':
            $actionObj = new TouiteDetailAction($this->touiteRepo);
            break;

        case 'user':
            $actionObj = new UserPageAction($this->utilisateurRepo, $this->touiteRepo);
            break;

        case 'influenceurs':
            $actionObj = new InfluenceursAction($this->utilisateurRepo);
            break;

        case 'tag':
            $actionObj = new TagAction($this->touiteRepo);
            break;

        case 'home':
        default:
            $actionObj = new HomeAction($this->touiteRepo);
            break;

        }

        $html = $actionObj->execute();

    } catch (\Throwable $e) {
        $html = "<div class='error' style='color: red; padding: 15px; border: 1px solid red; border-radius: 4px; margin: 10px 0;'>";
        $html .="<h2>Erreur</h2>";
        $html .= "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        $html .= "</div>";
    }

    $this->renderPage($html);

    }

    private function renderPage(string $html): void
    {

    echo <<<HTML

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Touiteur</title>
</head>
<body style="font-family: sans-serif; max-width: 650px; margin: 20px auto; line-height: 1.5;">
    <header class="app-header">
        <nav style="display: flex; gap: 10px; margin-bottom: 15px;">
            <a href="?action=home">Accueil</a> |
            <a href="?action=explore">Explorer</a> |
            <a href="?action=influenceurs">Influenceurs</a>
        </nav>
        <hr>
    </header>

    <main class="main-content">
        {$html}
    </main>

    <footer>
        <hr>
        <p>&copy; Touiteur</p>
    </footer>
</body>
</html>
HTML;
    }
}