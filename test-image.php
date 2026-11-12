<?php

echo "Dossier du projet : ";
echo realpath('.');

echo "<br><br>";

echo "Dossier uploads : ";
echo realpath('public/uploads');

echo "<br><br>";

echo "Fichiers dans uploads : ";

$fichiers = glob('public/uploads/*');

echo "<pre>";
print_r($fichiers);
echo "</pre>";

?>