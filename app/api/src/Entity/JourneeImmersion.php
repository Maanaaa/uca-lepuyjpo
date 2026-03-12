<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\JourneeImmersionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToOne(inversedBy: 'journeeImmersions')]
    private ?Departement $departement = null;

    /**
     * @var Collection<int, Cours>
     */
    #[ORM\OneToMany(targetEntity: Cours::class, mappedBy: 'journeeImmersion')]
    private Collection $cours;

    /**
     * @var Collection<int, InscriptionImmersion>
     */
    #[ORM\OneToMany(mappedBy: 'journeeImmersion', targetEntity: InscriptionImmersion::class, orphanRemoval: true)]
    private Collection $inscriptions;

    public function __construct()
    {
        $this->cours = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string {
        // Ex: "MMI - 12/03/2026"
        return $this->departement->getNom() . ' - ' . $this->date->format('d/m/Y');
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
     * @return Collection<int, Cours>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): static
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
            $cour->setJourneeImmersion($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): static
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getJourneeImmersion() === $this) {
                $cour->setJourneeImmersion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InscriptionImmersion>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(InscriptionImmersion $inscription): static
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->setJourneeImmersion($this);
        }

        return $this;
    }

    public function removeInscription(InscriptionImmersion $inscription): static
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getJourneeImmersion() === $this) {
                $inscription->setJourneeImmersion(null);
            }
        }

        return $this;
    }
}
