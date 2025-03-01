<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Email(
        message: "L'email '{{ value }}' n'est pas une adresse email valide."
    )]
    private ?string $email = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Length(min:2,
    max:10,
    minMessage: 'Votre nom doit etre au moins 2 characteres',
    maxMessage: 'Votre nom ne doit pas depasser 10 characteres')]
    private ?string $nom = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Length(min:2,
    max:10,
    minMessage: 'Votre prenom doit etre au moins 2 characteres',
    maxMessage: 'Votre prenom ne doit pas depasser 10 characteres')]
    private ?string $prenom = null;


    #[ORM\Column(type: 'date', nullable: true)] 
    #[Assert\NotBlank]
    #[Assert\LessThan("-7 years", message: "Vous devez avoir au moins 7 ans.")]
    #[Assert\GreaterThan("-60 years", message: "Vous devez avoir moins de 60 ans.")]
    private ?\DateTimeInterface $dateNaissance = null;
    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];
    

    /**
     * @var string The hashed password
     */
   
     #[Assert\NotBlank]
     #[Assert\Length(min: 8, max: 20, minMessage: "Le mot de passe doit contenir au moins 8 caractères.")]
     #[Assert\Regex(
         pattern: "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/",
         message: "Le mot de passe doit contenir au moins une lettre et un chiffre."
     )]
     #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
{
    return $this->dateNaissance;
}

public function setDateNaissance(?\DateTimeInterface $dateNaissance): self
{
    $this->dateNaissance = $dateNaissance;
    return $this;
}

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        if (empty($roles)) {
            $roles[] = 'ROLE_USER'; 
        }
    
        return array_unique($roles);
    }
    

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
    
        return $this;
    }
    
    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
    // In your User entity class
private ?string $plainPassword = null;

public function getPlainPassword(): ?string
{
    return $this->plainPassword;
}

public function setPlainPassword(?string $plainPassword): self
{
    $this->plainPassword = $plainPassword;

    return $this;
}
public function getAge(): ?int
{
    if ($this->dateNaissance === null) {
        return null;
    }
    $today = new \DateTime();
    return $this->dateNaissance->diff($today)->y;
}




}
