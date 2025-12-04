<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'setting', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\Column]
    private bool $isGameStarted = false;

    #[ORM\Column]
    private int $gameDuration = 25;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $finalCode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function isGameStarted(): bool
    {
        return $this->isGameStarted;
    }

    public function setGameStarted(bool $isGameStarted): static
    {
        $this->isGameStarted = $isGameStarted;

        return $this;
    }

    public function getGameDuration(): int
    {
        return $this->gameDuration;
    }

    public function setGameDuration(int $gameDuration): static
    {
        $this->gameDuration = $gameDuration;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeInterface $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getFinalCode(): ?string
    {
        return $this->finalCode;
    }

    public function setFinalCode(string $finalCode): static
    {
        $this->finalCode = $finalCode;

        return $this;
    }
}
