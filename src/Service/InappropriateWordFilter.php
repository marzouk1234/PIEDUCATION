<?php

// src/Service/InappropriateWordFilter.php
namespace App\Service;

class InappropriateWordFilter
{
    // Liste des mots inappropriés
    private $inappropriateWords;

    /**
     * Constructeur du service.
     *
     * @param array $inappropriateWords La liste des mots inappropriés à filtrer.
     */
    public function __construct(array $inappropriateWords)
    {
        $this->inappropriateWords = $inappropriateWords;
    }

    /**
     * Vérifie si un texte contient des mots inappropriés.
     *
     * @param string $text Le texte à vérifier.
     * @return bool True si un mot inapproprié est trouvé, sinon False.
     */
    public function containsInappropriateWords(string $text): bool
    {
        // Parcourir la liste des mots inappropriés
        foreach ($this->inappropriateWords as $word) {
            // Vérifier si le mot est présent dans le texte (insensible à la casse)
            if (stripos($text, $word) !== false) {
                return true; // Un mot inapproprié a été trouvé
            }
        }

        return false; // Aucun mot inapproprié trouvé
    }

    /**
     * Filtre les mots inappropriés dans un texte.
     *
     * @param string $text Le texte à filtrer.
     * @return string Le texte filtré.
     */
    public function filterInappropriateWords(string $text): string
    {
        // Parcourir la liste des mots inappropriés
        foreach ($this->inappropriateWords as $word) {
            // Remplacer chaque mot inapproprié par "***" (insensible à la casse)
            $text = str_ireplace($word, '***', $text);
        }

        return $text; // Retourner le texte filtré
    }
}