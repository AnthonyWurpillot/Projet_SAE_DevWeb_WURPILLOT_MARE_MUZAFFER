<?php

namespace iutnc\touiteur\domain;

class Touite {
private int $idTouite;
private string $texte;
private string $datePublication;
private int $idAuteur;
private ?string $pseudoAuteur;

public function __construct(
int $idTouite,
string $texte,
string $datePublication,
int $idAuteur,
?string $pseudoAuteur = null
) {

$this->idTouite = $idTouite;
$this->texte = $texte;
$this->datePublication = $datePublication;
$this->idAuteur = $idAuteur;
$this->pseudoAuteur = $pseudoAuteur;
}

public function getIdTouite(): int {
return $this->idTouite;
}

public function getTexte(): string {
return $this->texte;
}

public function getDatePublication(): string {
return $this->datePublication;
}

public function getIdAuteur(): int {
return $this->idAuteur;
}

public function getPseudoAuteur(): ?string {
return $this->pseudoAuteur;
}
}

