<?php

namespace App\Entity;

use App\Repository\ThumbnailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ThumbnailRepository::class)]
class Thumbnail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'thumbnails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Enigma $enigma = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $information = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnigma(): ?Enigma
    {
        return $this->enigma;
    }

    public function setEnigma(?Enigma $enigma): static
    {
        $this->enigma = $enigma;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(?string $information): static
    {
        $this->information = $information;

        return $this;
    }
}
