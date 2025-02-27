<?php

namespace App\Entity;

use App\Repository\AideRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
//
use Symfony\Component\Validator\Constraints as Assert;
//
#[ORM\Entity(repositoryClass: AideRepository::class)]
class Aide
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le sujet est obligatoire.")]
    #[Assert\Length(
        min: 5,
        max: 100,
        minMessage: "Le sujet doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le sujet ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $sujet = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    #[Assert\Length(
        min: 10,
        max: 1000,
        minMessage: "La description doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "La date de création est requise.")]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\ManyToOne(targetEntity: FormP::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Un formulaire doit être sélectionné.")]
    private ?FormP $form = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): static
    {
        $this->sujet = $sujet;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;
        return $this;
    }

    public function getForm(): ?FormP
    {
        return $this->form;
    }

    public function setForm(?FormP $form): static
    {
        $this->form = $form;
        return $this;
    }
}
