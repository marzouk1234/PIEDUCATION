<?php
// src/Entity/Feedback.php

namespace App\Entity;

use App\Repository\FeedbackRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "L'ID du jeu est obligatoire.")]
    #[Assert\Type(type: "integer", message: "L'ID du jeu doit être un nombre entier.")]
    private ?int $id_jeux = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    private ?string $prenom = null;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "Le feedback ne peut pas être vide.")]
    private ?string $feedback = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getIdJeux(): ?int
    {
        return $this->id_jeux;
    }

    public function setIdJeux(int $id_jeux): self
    {
        $this->id_jeux = $id_jeux;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getFeedback(): ?string
    {
        return $this->feedback;
    }

    public function setFeedback(string $feedback): self
    {
        $this->feedback = $feedback;
        return $this;
    }
}
