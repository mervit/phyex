<?php

namespace App\Entity;

use App\Repository\EvaluationParamRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvaluationParamRepository::class)]
class EvaluationParam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'evaluationParams')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Evaluation $evaluation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExerciseTypeParam $exerciseTypeParam = null;

    #[ORM\Column]
    private ?int $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvaluation(): ?Evaluation
    {
        return $this->evaluation;
    }

    public function setEvaluation(?Evaluation $evaluation): self
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    public function getExerciseTypeParam(): ?ExerciseTypeParam
    {
        return $this->exerciseTypeParam;
    }

    public function setExerciseTypeParam(?ExerciseTypeParam $exerciseTypeParam): self
    {
        $this->exerciseTypeParam = $exerciseTypeParam;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }
}
