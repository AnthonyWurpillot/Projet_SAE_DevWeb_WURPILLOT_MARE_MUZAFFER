<?php
declare(strict_types=1);

namespace iutnc\touiteur\traitement;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\repository\TagRepository;

class PublierTouite
{
    private TouiteRepository $touiteRepository;
    private TagRepository $tagRepository;

    public function __construct(
        TouiteRepository $touiteRepository,
        TagRepository $tagRepository
    ) {
        $this->touiteRepository = $touiteRepository;
        $this->tagRepository = $tagRepository;
    }

    public function execute(string $texte, ?array $image = null): int    {
        $texte = trim($texte);

        if ($texte === "") {
            throw new \Exception("Le touite ne peut pas être vide.");
        }

        if (mb_strlen($texte) > 235) {
            throw new \Exception("Le touite ne peut pas dépasser 235 caractères.");
        }

        if (!isset($_SESSION['idUtilisateur'])) {
            throw new \Exception("Vous devez être connecté pour publier.");
        }

        $idAuteur = (int) $_SESSION['idUtilisateur'];

        // Vérification de l'image
        $extension = null;

        if ($image !== null && $image['error'] !== UPLOAD_ERR_NO_FILE) {

            if ($image['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception("Erreur lors de l'envoi de l'image.");
            }

            if ($image['size'] > 5 * 1024 * 1024) {
                throw new \Exception("L'image ne doit pas dépasser 5 Mo.");
            }

            $fichier = finfo_open(FILEINFO_MIME_TYPE);
            $type = finfo_file($fichier, $image['tmp_name']);
            finfo_close($fichier);

            $formats = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp'
            ];

            if (!isset($formats[$type])) {
                throw new \Exception("Format d'image non autorisé.");
            }

            $extension = $formats[$type];
        }

        // Extraction des hashtags
        preg_match_all('/#([\p{L}\p{N}_]+)/u', $texte, $resultat);
        $tags = array_unique($resultat[1]);

        // Création du touite
        $idTouite = $this->touiteRepository->create($texte, $idAuteur);

        // Enregistrement de l'image
        if ($extension !== null) {

            $nomImage = bin2hex(random_bytes(8)) . '.' . $extension;

            $dossier = 'public/uploads/';
            $cheminImage = $dossier . $nomImage;

            if (!is_dir($dossier)) {
                mkdir($dossier, 0755, true);
            }

            if (!move_uploaded_file($image['tmp_name'], $cheminImage)) {
                throw new \Exception("Impossible d'enregistrer l'image.");
            }

            try {
                $this->touiteRepository->ajouterImage(
                    $cheminImage,
                    null,
                    $idTouite
                );
            } catch (\Throwable $e) {
                if (file_exists($cheminImage)) {
                    unlink($cheminImage);
                }

                throw $e;
            }
        }

        // Enregistrement des hashtags
        foreach ($tags as $libelle) {

            $idTag = $this->tagRepository->getTagIdByNom($libelle);

            if ($idTag === null) {
                $idTag = $this->tagRepository->createTag($libelle);
            }

            $this->tagRepository->associerTagAuTouite(
                $idTouite,
                $idTag
            );
        }

        return $idTouite;
    }
}
