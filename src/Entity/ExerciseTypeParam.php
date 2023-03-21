<?php

namespace App\Entity;

use App\Repository\ExerciseTypeParamRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciseTypeParamRepository::class)]
class ExerciseTypeParam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(cascade:["persist"], inversedBy: 'exerciseTypeParams')]
    private ?ExerciseType $exerciseType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
}
