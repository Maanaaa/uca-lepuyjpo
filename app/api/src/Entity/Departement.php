<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DepartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartementRepository::class)]
#[ApiResource]
class Departement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * @var Collection<int, Visite>
     */
    #[ORM\OneToMany(targetEntity: Visite::class, mappedBy: 'departement')]
    private Collection $visites;

    /**
     * @var Collection<int, JourneeImmersion>
     */
    #[ORM\OneToMany(targetEntity: JourneeImmersion::class, mappedBy: 'departement')]
    private Collection $journeeImmersions;

    /**
     * @var Collection<int, Etudiant>
     */
    #[ORM\OneToMany(targetEntity: Etudiant::class, mappedBy: 'departement')]
    private Collection $etudiants;

    /**
     * @var Collection<int, Visiteur>
     */
    #[ORM\OneToMany(targetEntity: Visiteur::class, mappedBy: 'departement')]
    private Collection $visiteurs;

    public function __construct()
    {
        $this->visites = new ArrayCollection();
        $this->journeeImmersions = new ArrayCollection();
        $this->etudiants = new ArrayCollection();
        $this->visiteurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Visite>
     */
    public function getVisites(): Collection
    {
        return $this->visites;
    }

    public function addVisite(Visite $visite): static
    {
        if (!$this->visites->contains($visite)) {
            $this->visites->add($visite);
            $visite->setDepartement($this);
        }

        return $this;
    }

    public function removeVisite(Visite $visite): static
    {
        if ($this->visites->removeElement($visite)) {
            // set the owning side to null (unless already changed)
            if ($visite->getDepartement() === $this) {
                $visite->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, JourneeImmersion>
     */
    public function getJourneeImmersions(): Collection
    {
        return $this->journeeImmersions;
    }

    public function addJourneeImmersion(JourneeImmersion $journeeImmersion): static
    {
        if (!$this->journeeImmersions->contains($journeeImmersion)) {
            $this->journeeImmersions->add($journeeImmersion);
            $journeeImmersion->setDepartement($this);
        }

        return $this;
    }

    public function removeJourneeImmersion(JourneeImmersion $journeeImmersion): static
    {
        if ($this->journeeImmersions->removeElement($journeeImmersion)) {
            // set the owning side to null (unless already changed)
            if ($journeeImmersion->getDepartement() === $this) {
                $journeeImmersion->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Etudiant>
     */
    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }

    public function addEtudiant(Etudiant $etudiant): static
    {
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants->add($etudiant);
            $etudiant->setDepartement($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): static
    {
        if ($this->etudiants->removeElement($etudiant)) {
            // set the owning side to null (unless already changed)
            if ($etudiant->getDepartement() === $this) {
                $etudiant->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Visiteur>
     */
    public function getVisiteurs(): Collection
    {
        return $this->visiteurs;
    }

    public function addVisiteur(Visiteur $visiteur): static
    {
        if (!$this->visiteurs->contains($visiteur)) {
            $this->visiteurs->add($visiteur);
            $visiteur->setDepartement($this);
        }

        return $this;
    }

    public function removeVisiteur(Visiteur $visiteur): static
    {
        if ($this->visiteurs->removeElement($visiteur)) {
            // set the owning side to null (unless already changed)
            if ($visiteur->getDepartement() === $this) {
                $visiteur->setDepartement(null);
            }
        }

        return $this;
    }
}
