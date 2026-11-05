<?php

declare(strict_types=1);

namespace iutnc\touiteur\domain;

class Tag {
private int $idTag;
private string $libelle;
private ?string $description;

public function __construct(
int $idTag,
string $libelle,
?string $description = null
) {

$this->idTag = $idTag;
$this->libelle = $libelle;
$this->description = $description;
}

public function getIdTag(): int {
return $this->idTag;
}

public function getLibelle(): string {
return $this->libelle;
}

public function getDescription(): ?string {
return $this->description;
}
}