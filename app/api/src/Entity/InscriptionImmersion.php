<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource]
class InscriptionImmersion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Visiteur::class, inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Visiteur $visiteur = null;

    #[ORM\ManyToOne(targetEntity: JourneeImmersion::class, inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?JourneeImmersion $journeeImmersion = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $inscritLe = null;

    public function __construct()
    {
        $this->inscritLe = new \DateTimeImmutable();
    }

    // Getters/Setters...
    public function getId(): ?int { return $this->id; }
    public function getVisiteur(): ?Visiteur { return $this->visiteur; }
    public function setVisiteur(Visiteur $visiteur): static 
    { 
        $this->visiteur = $visiteur; 
        return $this; 
    }

    public function getInscritLe(): ?\DateTimeImmutable
    {
    return $this->inscritLe;
    }

    
    public function getJourneeImmersion(): ?JourneeImmersion { return $this->journeeImmersion; }
    public function setJourneeImmersion(JourneeImmersion $journeeImmersion): static 
    { 
        $this->journeeImmersion = $journeeImmersion; 
        return $this; 
    }
}
