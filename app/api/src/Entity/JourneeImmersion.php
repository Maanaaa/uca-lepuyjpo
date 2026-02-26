<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\JourneeImmersionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JourneeImmersionRepository::class)]
#[ApiResource]
class JourneeImmersion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column]
    private ?int $departement_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDepartementId(): ?int
    {
        return $this->departement_id;
    }

    public function setDepartementId(int $departement_id): static
    {
        $this->departement_id = $departement_id;

        return $this;
    }
}
