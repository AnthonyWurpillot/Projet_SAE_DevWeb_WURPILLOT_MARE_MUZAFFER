<?php

namespace iutnc\touiteur\action;

class ActionSuivreUtilisateur
{
    private $suivreRepository;

        public function __construct($suivreRepository)
        {
            $this->suivreRepository = $suivreRepository;
        }

            public function execute($idUtilisateurASuivre)
            {

               if(!ISSET($_SESSION['idutilisateur'])){
               throw new \Exception("Vous devez être connecté pour publier.")};

               $idSuiveur = $_SESSION['idUtilisateur'];

                if ($idUtilisateurASuivre === null || $idUtilisateurASuivre === '') {
                           throw new \Exception("Utilisateur introuvable.");
                       }

                if ($idSuiveur == $idUtilisateurASuivre) {
                     throw new \Exception("Vous ne pouvez pas vous suivre vous-même.");
                }


                // Ajouter la relation dans la base de données
    }
}