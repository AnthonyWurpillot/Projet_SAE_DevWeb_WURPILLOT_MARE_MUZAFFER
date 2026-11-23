<?php
// 1.1 - EXPONENTIATION RAPIDE
function expmod($x, $e, $n) {
    $resultat = 1;

    while ($e > 0) {
        if ($e % 2 == 1) {
            $resultat = ($resultat * $x) % $n;
        }

        $x = ($x * $x) % $n;
        $e = intdiv($e, 2);
    }

    return $resultat;
}
// 1.2 - PGCD (ALGORITHME D'EUCLIDE)
function gcd($a, $b) {
    while ($b != 0) {
        $reste = $a % $b;
        $a = $b;
        $b = $reste;
    }

    return $a;
}
// 1.3 - INVERSE MODULAIRE

function inversemod($e, $f) {
    $r0 = $e;
    $u0 = 1;
    $r1 = $f;
    $u1 = 0;

    while ($r1 > 0) {
        $q = intdiv($r0, $r1);

        $tempR = $r0 - $q * $r1;
        $tempU = $u0 - $q * $u1;

        $r0 = $r1;
        $u0 = $u1;
        $r1 = $tempR;
        $u1 = $tempU;
    }

    if ($r0 != 1) {
        return "Erreur : e n'est pas inversible.";
    }

    return (($u0 % $f) + $f) % $f;
}


// 1.4 - CHIFFREMENT ET DECHIFFREMENT RSA

function RSAencrypt($x, $e, $n) {
    return expmod($x, $e, $n);
}

function RSAdecrypt($y, $e, $p, $q) {
    $n = $p * $q;
    $phi = ($p - 1) * ($q - 1);
    $d = inversemod($e, $phi);

    if (!is_numeric($d)) {
        return $d;
    }

    return expmod($y, $d, $n);
}
// 1.5 - QUESTION PRELIMINAIRE 1 : TEST DE FERMAT

function nombreTemoinsFermat($n) {
    $nombreTemoins = 0;

    for ($a = 2; $a < $n; $a++) {
        if (gcd($a, $n) == 1) {
            if (expmod($a, $n - 1, $n) != 1) {
                $nombreTemoins++;
            }
        }
    }

    return $nombreTemoins;
}
// 1.5 - A) TEMOIN DE NON-PRIMALITE

function estTemoin($a, $d, $s, $n) {
    $x = expmod($a, $d, $n);

    if ($x == 1 || $x == $n - 1) {
        return false;
    }

    for ($i = 1; $i < $s; $i++) {
        $x = ($x * $x) % $n;

        if ($x == $n - 1) {
            return false;
        }
    }

    return true;
}
// 1.5 - B) TEST DE MILLER-RABIN

function millerRabin($n, $k) {

    if ($n < 2) {
        return false;
    }

    if ($n == 2 || $n == 3) {
        return true;
    }

    if ($n % 2 == 0) {
        return false;
    }

    // Décomposition de n - 1 = 2^s * d
    $d = $n - 1;
    $s = 0;

    while ($d % 2 == 0) {
        $d = intdiv($d, 2);
        $s++;
    }

    // Tirage de k bases aléatoires
    for ($i = 0; $i < $k; $i++) {
        $a = random_int(2, $n - 2);

        if (estTemoin($a, $d, $s, $n)) {
            return false;
        }
    }

    return true;
}
// 1.5 - D) GENERATION D'UN NOMBRE PREMIER ALEATOIRE

function premierAleatoire($min, $max) {

    // On ne conserve que les nombres impairs
    if ($min % 2 == 0) {
        $min++;
    }

    if ($max % 2 == 0) {
        $max--;
    }

    if ($min > $max) {
        throw new Exception("Aucun nombre impair dans cet intervalle.");
    }

    $nombreImpairs = intdiv($max - $min, 2);

    while (true) {
        $n = $min + 2 * random_int(0, $nombreImpairs);

        if (millerRabin($n, 20)) {
            return $n;
        }
    }
}
// 1.5 - D) GENERATION DES CLES RSA

function genererClesRSA($min, $max) {

    // Génération de deux nombres premiers distincts
    $p = premierAleatoire($min, $max);
    $q = premierAleatoire($min, $max);

    while ($p == $q) {
        $q = premierAleatoire($min, $max);
    }

    // Calcul de n et de phi(n)
    $n = $p * $q;
    $phi = ($p - 1) * ($q - 1);

    // Recherche d'un exposant e premier avec phi(n)
    do {
        $e = random_int(2, $phi - 1);
    } while (gcd($e, $phi) != 1);

    // Calcul de l'exposant privé d
    $d = inversemod($e, $phi);

    return [
        "p" => $p,
        "q" => $q,
        "n" => $n,
        "phi" => $phi,
        "e" => $e,
        "d" => $d
    ];
}
// TESTS DES FONCTIONS

echo "===== TESTS 1.1 A 1.4 =====\n";

echo "expmod(2,30,9) : " . expmod(2, 30, 9) . "\n";
echo "gcd(12,26) : " . gcd(12, 26) . "\n";
echo "inversemod(5,26) : " . inversemod(5, 26) . "\n";
echo "inversemod(2,26) : " . inversemod(2, 26) . "\n";

echo "RSAencrypt(3,5,85) : " . RSAencrypt(3, 5, 85) . "\n";
echo "RSAdecrypt(2,5,5,17) : " . RSAdecrypt(2, 5, 5, 17) . "\n";


echo "\nTESTS 1.5\n";

// Question préliminaire 1
echo "Temoins Fermat pour 561 : " . nombreTemoinsFermat(561) . "\n";
echo "Temoins Fermat pour 15 : " . nombreTemoinsFermat(15) . "\n";

// Question 1.5 b et c
echo "Miller-Rabin(561,20) : " . (millerRabin(561, 20) ? "Premier" : "Compose") . "\n";
echo "Miller-Rabin(221,20) : " . (millerRabin(221, 20) ? "Premier" : "Compose") . "\n";
echo "Miller-Rabin(997,20) : " . (millerRabin(997, 20) ? "Premier" : "Compose") . "\n";
echo "Miller-Rabin(7919,20) : " . (millerRabin(7919, 20) ? "Premier" : "Compose") . "\n";

// Question 1.5 d
echo "\n GENERATION DE CLES RSA \n";

$cles = genererClesRSA(100, 999);

echo "p : " . $cles["p"] . "\n";
echo "q : " . $cles["q"] . "\n";
echo "n : " . $cles["n"] . "\n";
echo "phi(n) : " . $cles["phi"] . "\n";
echo "e : " . $cles["e"] . "\n";
echo "d : " . $cles["d"] . "\n";
?>
