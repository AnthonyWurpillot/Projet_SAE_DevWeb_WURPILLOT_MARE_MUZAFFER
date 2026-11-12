<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\UtilisateurRepository;

class LoginAction extends Action
{
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(UtilisateurRepository $utilisateurRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
    }

    public function execute(): string
    {
        $utilisateurs = $this->utilisateurRepository->findAll();

        $html = "<header class='main-header'>";
        $html .= "<h1>Connexion</h1>";
        $html .= "</header>";

        $html .= "<section class='login-portal'>";
        $html .= "<p>Choisissez un utilisateur :</p>";

        if ($utilisateurs === []) {
            $html .= "<p>Aucun utilisateur disponible.</p>";
        } else {
            $html .= "<form method='post' action='?action=login'>";

            foreach ($utilisateurs as $utilisateur) {
                $html .= "<button type='submit' name='idUtilisateur' value='" . $utilisateur->getIdUtilisateur() . "' class='nav-link'>";
                $html .= "@" . htmlspecialchars($utilisateur->getPseudo()) . "</button>";
            }

            $html .= "</form>";
        }

        $html .= "</section>";

        return $html;
    }
}
