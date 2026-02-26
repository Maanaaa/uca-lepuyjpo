<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource; #APIPlatform
use App\Repository\TestAPIPlatformRepository;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource] #APIPlatform
#[ORM\Entity(repositoryClass: TestAPIPlatformRepository::class)]
class TestAPIPlatform
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $hello = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHello(): ?string
    {
        return $this->hello;
    }

    public function setHello(string $hello): static
    {
        $this->hello = $hello;

        return $this;
    }
}
