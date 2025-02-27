<?php

namespace App\Entity;
//
use Symfony\Component\Validator\Constraints as Assert;
//
use App\Repository\FormPRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: FormPRepository::class)]
class FormP
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le contenu ne peut pas être vide.")]
    #[Assert\Length(
        min: 10,
        minMessage: "Le contenu doit avoir au moins {{ limit }} caractères."
    )]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull(message: "La date de publication est requise.")]
    #[Assert\LessThanOrEqual("today", message: "La date ne peut pas être dans le futur.")]
    private ?\DateTimeImmutable $date_pub = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le sujet est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: "Le sujet doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le sujet ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $sujet = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'auteur est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Le nom de l'auteur doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom de l'auteur ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $auteur = null;

    public function __construct()
    {
        $this->date_pub = new \DateTimeImmutable(); // Définit automatiquement la date actuelle
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getDatePub(): ?\DateTimeImmutable
    {
        return $this->date_pub;
    }

    public function setDatePub(\DateTimeImmutable $date_pub): static
    {
        $this->date_pub = $date_pub;
        return $this;
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

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): static
    {
        $this->auteur = $auteur;
        return $this;
    }

    public function __toString(): string
    {
        return $this->sujet . ' - ' . $this->auteur;
    }
}
