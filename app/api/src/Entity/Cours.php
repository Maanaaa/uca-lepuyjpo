<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CoursRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
#[ApiResource]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $matiere = null;

    #[ORM\Column]
    private ?int $journee_immersion_id = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    private ?JourneeImmersion $journeeImmersion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatiere(): ?string
    {
        return $this->matiere;
    }

    public function setMatiere(string $matiere): static
    {
        $this->matiere = $matiere;

        return $this;
    }

    public function getJourneeImmersionId(): ?int
    {
        return $this->journee_immersion_id;
    }

    public function setJourneeImmersionId(int $journee_immersion_id): static
    {
        $this->journee_immersion_id = $journee_immersion_id;

        return $this;
    }

    public function getJourneeImmersion(): ?JourneeImmersion
    {
        return $this->journeeImmersion;
    }

    public function setJourneeImmersion(?JourneeImmersion $journeeImmersion): static
    {
        $this->journeeImmersion = $journeeImmersion;

        return $this;
    }
}
