<?php

declare(strict_types=1);

namespace iutnc\touiteur\domain;

class Utilisateur {
private int $idUtilisateur;
private string $pseudo;
private ?string $nom;
private ?string $prenom;
private string $email;
private int $nbTouites;

public function __construct(
int $idUtilisateur,
string $pseudo,
?string $nom,
?string $prenom,
string $email,
int $nbTouites = 0
) {

$this->idUtilisateur = $idUtilisateur;
$this->pseudo = $pseudo;
$this->nom = $nom;
$this->prenom = $prenom;
$this->email = $email;
$this->nbTouites = $nbTouites;
}

public function getIdUtilisateur(): int {
return $this->idUtilisateur;
}

public function getPseudo(): string {
return $this->pseudo;
}

public function getNom(): ?string {
return $this->nom;
}

public function getPrenom(): ?string {
return $this->prenom;
}

public function getEmail(): string{
return $this->email;
}

public function getNbTouites(): int{
return $this->nbTouites;
}

public function incrementerNbTouites(): void {
    $this->nbTouites++;

}
}