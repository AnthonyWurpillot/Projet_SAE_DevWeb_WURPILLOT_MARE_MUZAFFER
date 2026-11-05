<?php

declare(strict_types=1);

namespace iutnc\touiteur\domain;

class Touite {
private int $idTouite;
private string $texte;
private string $datePublication;
private int $idAuteur;
private ?string $pseudoAuteur;
private array $tags;
private int $score;

public function __construct(
int $idTouite,
string $texte,
string $datePublication,
int $idAuteur,
?string $pseudoAuteur = null,
array $tags = [],
int $score = 0
) {

$this->idTouite = $idTouite;
$this->texte = $texte;
$this->datePublication = $datePublication;
$this->idAuteur = $idAuteur;
$this->pseudoAuteur = $pseudoAuteur;
$this->tags = $tags;
$this->score = $score;
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

public function getTags(): array {
return $this->tags;
}

public function setTags(array $tags): void {
$this->tags = $tags;
}

public function addTag(Tag $tag): void {
$this->tags[] = $tag;
}

public function getScore(): int {
return $this->score;
}

public function setScore(int $score): void {
$this->score = $score;
}

}

