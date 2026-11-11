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
use iutnc\touiteur\traitement\SuivreUtilisateur;
use iutnc\touiteur\traitement\AbonnerTag;

class Dispatcher
{

    private ?string $action;
    private TouiteRepository $touiteRepo;
    private UtilisateurRepository $utilisateurRepo;
    private TagRepository $tagRepo;

    public function __construct()
    {

    $this->action = $_GET['action'] ?? 'home';
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
                header('Location: ' . $this->redirectUrl());
                exit;
            }
            $actionObj = new HomeAction($this->touiteRepo);
            break;

        case 'suivre':
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                (new SuivreUtilisateur($this->utilisateurRepo))
                    ->execute($_POST['idUtilisateur'] ?? null);
                header('Location: ' . $this->redirectUrl());
                exit;
            }
            $actionObj = new HomeAction($this->touiteRepo);
            break;

        case 'abonnerTag':
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AbonnerTag($this->tagRepo))
                    ->execute($_POST['tag'] ?? '');
                header('Location: ' . $this->redirectUrl());
                exit;
            }
            $actionObj = new HomeAction($this->touiteRepo);
            break;

        case 'noter':
            throw new \Exception("Fonctionnalité à venir.");

        case'explore':
            $actionObj = new ExploreAction($this->touiteRepo);
            break;

        case 'tweet':
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

    private function redirectUrl(): string
    {
        $redirect = $_POST['redirect'] ?? '';

        if (!is_string($redirect) || !str_starts_with($redirect, '?action=')) {
            return '?action=home';
        }

        $actionRedirect = explode('&', substr($redirect, 8))[0];

        $actionsAutorisees = ['home', 'explore', 'tweet', 'user', 'tag', 'influenceurs'];

        if (in_array($actionRedirect, $actionsAutorisees, true)) {
            return $redirect;
        } else {
            return '?action=home';
        }
    }

    private function renderPage(string $html): void
    {

    $navHome = $this->action === 'home' ? ' active' : '';
    $navExplore = $this->action === 'explore' ? ' active' : '';
    $navInfluenceurs = $this->action === 'influenceurs' ? ' active' : '';

    echo <<<HTML

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Touiteur</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <header class="top-bar">
        <h1>Touiteur</h1>
    </header>

    <div class="container">
        <aside class="sidebar-left">
            <nav class="main-nav">
                <a href="?action=home" class="nav-link{$navHome}">Accueil</a>
                <a href="?action=explore" class="nav-link{$navExplore}">Explorer</a>
                <a href="?action=influenceurs" class="nav-link{$navInfluenceurs}">Influenceurs</a>
            </nav>
        </aside>

        <main class="main-content">
            {$html}
        </main>

        <aside class="sidebar-right">
            <div class="tag-subscription">
                <h2>Suivre un tag</h2>
                <form method="post" action="?action=abonnerTag">
                    <input type="text" name="tag" placeholder="#informatique" required>
                    <button type="submit">S'abonner</button>
                </form>
            </div>
        </aside>
    </div>
</body>
</html>
HTML;
    }
}
