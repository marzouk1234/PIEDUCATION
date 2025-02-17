<?php

namespace App\Entity;

use App\Repository\ResultatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ResultatRepository::class)]
class Resultat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La note ne peut pas être vide.")]
    #[Assert\Range(
        min: 0,
        max: 20,
        notInRangeMessage: "La note doit être comprise entre 0 et 20."
    )]
    private ?int $note = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "Veuillez renseigner une date.")]
    #[Assert\Type(type: "\DateTimeInterface", message: "La date doit être valide.")]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'appréciation est requise.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "L'appréciation ne peut pas dépasser 255 caractères."
    )]
    private ?string $appreciation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Une évaluation doit être sélectionnée.")]
    private ?Evaluation $Evaluation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getAppreciation(): ?string
    {
        return $this->appreciation;
    }

    public function setAppreciation(string $appreciation): static
    {
        $this->appreciation = $appreciation;
        return $this;
    }

    public function getEvaluation(): ?Evaluation
    {
        return $this->Evaluation;
    }

    public function setEvaluation(?Evaluation $Evaluation): static
    {
        $this->Evaluation = $Evaluation;
        return $this;
    }
}
