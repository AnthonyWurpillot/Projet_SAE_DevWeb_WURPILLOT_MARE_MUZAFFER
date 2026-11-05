<?php

declare(strict_types=1);

namespace iutnc\touiteur\domain;

class Note {

private int $idUtilisateur;
private int $idTouite;
private int $valeur;

public function __construct(int $idUtilisateur, int $idTouite, int $valeur) {
$this->idUtilisateur = $idUtilisateur;
$this->idTouite = $idTouite;
$this->valeur = $valeur;
}

public function getIdUtilisateur(): int {
return $this->idUtilisateur;
}

public function getIdTouite(): int {
return $this->idTouite;
}

public function getValeur(): int {
return $this->valeur;
}
}