<?php

namespace App\Entity;

use App\Repository\ExerciseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciseRepository::class)]
class Exercise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ["persist"], inversedBy: 'exercises')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Figurant $figurant = null;

    #[ORM\ManyToOne(cascade:["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExerciseType $exerciseType = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datetime = null;

    #[ORM\Column(length: 255)]
    private ?string $videoFront = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $videoSide = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $videoMidLeft = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $videoMidRight = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFigurant(): ?Figurant
    {
        return $this->figurant;
    }

    public function setFigurant(?Figurant $figurant): self
    {
        $this->figurant = $figurant;

        return $this;
    }

    public function getExerciseType(): ?ExerciseType
    {
        return $this->exerciseType;
    }

    public function setExerciseType(?ExerciseType $exerciseType): self
    {
        $this->exerciseType = $exerciseType;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getVideoFront(): ?string
    {
        return $this->videoFront;
    }

    public function setVideoFront(string $videoFront): self
    {
        $this->videoFront = $videoFront;

        return $this;
    }

    public function getVideoSide(): ?string
    {
        return $this->videoSide;
    }

    public function setVideoSide(?string $videoSide): self
    {
        $this->videoSide = $videoSide;

        return $this;
    }

    public function getVideoMidLeft(): ?string
    {
        return $this->videoMidLeft;
    }

    public function setVideoMidLeft(?string $videoMidLeft): self
    {
        $this->videoMidLeft = $videoMidLeft;

        return $this;
    }

    public function getVideoMidRight(): ?string
    {
        return $this->videoMidRight;
    }

    public function setVideoMidRight(?string $videoMidRight): self
    {
        $this->videoMidRight = $videoMidRight;

        return $this;
    }
}
