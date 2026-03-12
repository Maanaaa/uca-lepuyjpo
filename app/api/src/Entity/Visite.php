<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\VisiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Utilisateur;

#[ORM\Entity(repositoryClass: VisiteRepository::class)]
#[ApiResource(
    //mercure: true
)]
class Visite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $fin = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column (nullable: true)]
    private ?int $note = null;

    #[ORM\Column]
    private ?int $visiteur_id = null;



    #[ORM\ManyToOne(inversedBy: 'visites')]
    private ?Visiteur $visiteur = null;

    #[ORM\ManyToOne(inversedBy: 'visites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Departement $departement = null;

    /**
     * @var Collection<int, Utilisateur>
     */
    #[ORM\OneToMany(targetEntity: Utilisateur::class, mappedBy: 'visite')]
    private Collection $utilisateurs;

    #[ORM\ManyToOne(inversedBy: 'visites')]
    private ?Utilisateur $etudiant = null;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDebut(): ?\DateTime
    {
        return $this->debut;
    }

    public function setDebut(\DateTime $debut): static
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTime
    {
        return $this->fin;
    }

    public function setFin(\DateTime $fin): static
    {
        $this->fin = $fin;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
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

    public function getVisiteurId(): ?int
    {
        return $this->visiteur_id;
    }

    public function setVisiteurId(int $visiteur_id): static
    {
        $this->visiteur_id = $visiteur_id;

        return $this;
    }


    public function getVisiteur(): ?Visiteur
    {
        return $this->visiteur;
    }

    public function setVisiteur(?Visiteur $visiteur): static
    {
        $this->visiteur = $visiteur;

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

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): static
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->add($utilisateur);
            $utilisateur->setVisite($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getVisite() === $this) {
                $utilisateur->setVisite(null);
            }
        }

        return $this;
    }

    public function getEtudiant(): ?Utilisateur
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Utilisateur $etudiant): static
    {
        $this->etudiant = $etudiant;
        return $this;
    }

}