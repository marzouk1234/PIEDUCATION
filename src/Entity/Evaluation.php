<?php

// src/Entity/Evaluation.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Evaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    #[Assert\Length(min: 3, minMessage: "Le titre doit contenir au moins 3 caractères.")]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ][a-zA-ZÀ-ÿ0-9\s\-'!?,.]*$/",
        message: "Le titre doit commencer par une lettre et ne peut pas être composé uniquement de chiffres."
    )]
    private $titre;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "La date est obligatoire.")]
    private $date;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: "Le type est obligatoire.")]
    private $type;


    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }
    
    public function getAppreciationBasedOnNote(): string
    {
        $note = $this->note;

        if ($note >= 0 && $note < 10) {
            return 'Insuffisant';
        } elseif ($note >= 10 && $note < 13) {
            return 'Passable';
        } elseif ($note >= 13 && $note < 15) {
            return 'Bien';
        } elseif ($note >= 15 && $note <= 20) {
            return 'Très bien';
        }

        return 'Non défini';
    }
}
