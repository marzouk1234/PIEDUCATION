<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity(fields: ['titre'], message: 'Ce titre existe déjà.')]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(min: 3, minMessage: 'Le titre doit contenir au moins 3 caractères.')]
    private ?string $titre = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'Le nombre de chapitres est obligatoire.')]
    private ?int $nbchapitre = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Regex(
        pattern: '/[A-Z]/',
        message: 'La description doit contenir au moins une lettre majuscule.'
    )]
    private ?string $description = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: 'La date est obligatoire.')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\File(
        mimeTypes: ['application/pdf'],
        mimeTypesMessage: 'Le fichier doit être un PDF valide.'
    )]
    private ?string $pdf = null;

    #[ORM\ManyToMany(targetEntity: Inscription::class, mappedBy: 'cours')]
    private Collection $inscriptions;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = ucfirst($titre);
        return $this;
    }

    public function getNbchapitre(): ?int
    {
        return $this->nbchapitre;
    }

    public function setNbchapitre(int $nbchapitre): self
    {
        $this->nbchapitre = $nbchapitre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getPdf(): ?string
    {
        return $this->pdf;
    }

    public function setPdf(?string $pdf): self
    {
        $this->pdf = $pdf;
        return $this;
    }

    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
        }
        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        $this->inscriptions->removeElement($inscription);
        return $this;
    }
}
