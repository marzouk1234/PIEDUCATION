<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Inscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Cours::class, inversedBy: 'inscriptions')]
    #[ORM\JoinTable(name: 'inscription_cours')]
    private Collection $cours;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: 'La date d\'inscription est obligatoire.')]
    private ?\DateTimeInterface $dateInscription = null;

    public function __construct()
    {
        $this->cours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCours(Cours $cours): self
    {
        if (!$this->cours->contains($cours)) {
            $this->cours->add($cours);
        }
        return $this;
    }

    public function removeCours(Cours $cours): self
    {
        $this->cours->removeElement($cours);
        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;
        return $this;
    }
    /*
    #[ORM\Column(type: 'string', length: 50)]
#[Assert\NotBlank(message: 'Le plan est obligatoire.')]
private ?string $plan = null;

public function getPlan(): ?string
{
    return $this->plan;
}

public function setPlan(string $plan): self
{
    $this->plan = $plan;
    return $this;
}
*/
}
