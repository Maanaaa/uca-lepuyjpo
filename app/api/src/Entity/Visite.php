<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\VisiteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisiteRepository::class)]
#[ApiResource]
class Visite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $fin = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?int $note = null;

    #[ORM\Column]
    private ?int $visiteur_id = null;

    #[ORM\Column]
    private ?int $etudiant_id = null;

    #[ORM\ManyToOne(inversedBy: 'visites')]
    private ?Visiteur $visiteur = null;

    #[ORM\ManyToOne(inversedBy: 'visites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Departement $departement = null;

    #[ORM\ManyToOne(inversedBy: 'visites')]
    private ?Etudiant $etudiant = null;

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

    public function getEtudiantId(): ?int
    {
        return $this->etudiant_id;
    }

    public function setEtudiantId(int $etudiant_id): static
    {
        $this->etudiant_id = $etudiant_id;

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

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }
}
