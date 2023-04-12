<?php

namespace App\Entity;

use App\Repository\EvaluationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvaluationRepository::class)]
class Evaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exercise $exercise = null;

    #[ORM\OneToMany(mappedBy: 'evaluation', targetEntity: EvaluationParam::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $evaluationParams;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;

    public function __construct()
    {
        $this->evaluationParams = new ArrayCollection();
        $this->created = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getExercise(): ?Exercise
    {
        return $this->exercise;
    }

    public function setExercise(?Exercise $exercise): self
    {
        $this->exercise = $exercise;

        return $this;
    }

    /**
     * @return Collection<int, EvaluationParam>
     */
    public function getEvaluationParams(): Collection
    {
        return $this->evaluationParams;
    }

    public function addEvaluationParam(EvaluationParam $evaluationParam): self
    {
        if (!$this->evaluationParams->contains($evaluationParam)) {
            $this->evaluationParams->add($evaluationParam);
            $evaluationParam->setEvaluation($this);
        }

        return $this;
    }

    public function removeEvaluationParam(EvaluationParam $evaluationParam): self
    {
        if ($this->evaluationParams->removeElement($evaluationParam)) {
            // set the owning side to null (unless already changed)
            if ($evaluationParam->getEvaluation() === $this) {
                $evaluationParam->setEvaluation(null);
            }
        }

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }
}
