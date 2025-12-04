<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $welcomeMsg = null;

    #[ORM\Column(length: 255)]
    private ?string $welcomeImg = null;

    #[ORM\OneToOne(mappedBy: 'game', cascade: ['persist', 'remove'])]
    private ?Setting $setting = null;

    #[ORM\OneToMany(targetEntity: Enigma::class, mappedBy: 'game', orphanRemoval: true)]
    private Collection $enigmas;

    #[ORM\OneToMany(targetEntity: Team::class, mappedBy: 'game', orphanRemoval: true)]
    private Collection $teams;

    public function __construct()
    {
        $this->enigmas = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getWelcomeMsg(): ?string
    {
        return $this->welcomeMsg;
    }

    public function setWelcomeMsg(string $welcomeMsg): static
    {
        $this->welcomeMsg = $welcomeMsg;

        return $this;
    }

    public function getWelcomeImg(): ?string
    {
        return $this->welcomeImg;
    }

    public function setWelcomeImg(string $welcomeImg): static
    {
        $this->welcomeImg = $welcomeImg;

        return $this;
    }

    public function getSetting(): ?Setting
    {
        return $this->setting;
    }

    public function setSetting(Setting $setting): static
    {
        if ($setting->getGame() !== $this) {
            $setting->setGame($this);
        }

        $this->setting = $setting;

        return $this;
    }

    public function getEnigmas(): Collection
    {
        return $this->enigmas;
    }

    public function addEnigma(Enigma $enigma): static
    {
        if (!$this->enigmas->contains($enigma)) {
            $this->enigmas->add($enigma);
            $enigma->setGame($this);
        }

        return $this;
    }

    public function removeEnigma(Enigma $enigma): static
    {
        if ($this->enigmas->removeElement($enigma)) {
            if ($enigma->getGame() === $this) {
                $enigma->setGame(null);
            }
        }

        return $this;
    }

    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): static
    {
        if (!$this->teams->contains($team)) {
            $this->teams->add($team);
            $team->setGame($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): static
    {
        if ($this->teams->removeElement($team)) {
            if ($team->getGame() === $this) {
                $team->setGame(null);
            }
        }

        return $this;
    }
}
