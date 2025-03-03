<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Evaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    #[Assert\Length(min: 3, minMessage: "Le titre doit contenir au moins 3 caractères.")]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ][a-zA-ZÀ-ÿ0-9\s\-'!?,.]*$/",
        message: "Le titre doit commencer par une lettre et ne peut pas être composé uniquement de chiffres."
    )]
    private ?string $titre = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "La date est obligatoire.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: "Le type est obligatoire.")]
    private ?string $type = null;

    #[ORM\ManyToMany(targetEntity: Etudiant::class, inversedBy: 'evaluations')]
    #[ORM\JoinTable(name: 'etudiant_evaluation')]
    private Collection $etudiants;

    public function __construct()
    {
        $this->etudiants = new ArrayCollection();
    }

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

    public function setDate(?\DateTimeInterface $date): static
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

    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }

    public function addEtudiant(Etudiant $etudiant): static
    {
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants->add($etudiant);
            $etudiant->addEvaluation($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): static
    {
        if ($this->etudiants->removeElement($etudiant)) {
            $etudiant->removeEvaluation($this);
        }

        return $this;
    }
}
