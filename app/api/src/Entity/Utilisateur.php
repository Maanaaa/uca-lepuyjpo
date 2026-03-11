<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Get(security: "is_granted('ROLE_USER')"),
        new Patch(security: "is_granted('ROLE_USER') and object == user")
    ]
)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    private ?Departement $departement = null;

    #[ORM\Column]
    private ?bool $isDisponible = null;

    #[ORM\OneToMany(targetEntity: Visite::class, mappedBy: 'etudiant')]
    private Collection $visites;

    /**
     * @var Collection<int, PushSubscription>
     */
    #[ORM\OneToMany(targetEntity: PushSubscription::class, mappedBy: 'utilisateur')]
    private Collection $pushSubscriptions;

    public function __construct()
    {
        $this->visites = new ArrayCollection();
        $this->pushSubscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): static
    {
        $this->departement = $departement;
        return $this;
    }

    public function isDisponible(): ?bool
    {
        return $this->isDisponible;
    }

    public function setIsDisponible(bool $isDisponible): static
    {
        $this->isDisponible = $isDisponible;
        return $this;
    }

    public function getVisites(): Collection
    {
        return $this->visites;
    }

    public function addVisite(Visite $visite): static
    {
        if (!$this->visites->contains($visite)) {
            $this->visites->add($visite);
            $visite->setEtudiant($this);
        }
        return $this;
    }

    public function removeVisite(Visite $visite): static
    {
        if ($this->visites->removeElement($visite)) {
            if ($visite->getEtudiant() === $this) {
                $visite->setEtudiant(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * @return Collection<int, PushSubscription>
     */
    public function getPushSubscriptions(): Collection
    {
        return $this->pushSubscriptions;
    }

    public function addPushSubscription(PushSubscription $pushSubscription): static
    {
        if (!$this->pushSubscriptions->contains($pushSubscription)) {
            $this->pushSubscriptions->add($pushSubscription);
            $pushSubscription->setUtilisateur($this);
        }

        return $this;
    }

    public function removePushSubscription(PushSubscription $pushSubscription): static
    {
        if ($this->pushSubscriptions->removeElement($pushSubscription)) {
            // set the owning side to null (unless already changed)
            if ($pushSubscription->getUtilisateur() === $this) {
                $pushSubscription->setUtilisateur(null);
            }
        }

        return $this;
    }
}