<?php

namespace App\Entity;

use App\Repository\ExerciseTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciseTypeRepository::class)]
class ExerciseType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'exerciseType', targetEntity: ExerciseTypeParam::class, cascade: ["persist"])]
    private Collection $exerciseTypeParams;

    public function __construct()
    {
        $this->exerciseTypeParams = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, ExerciseTypeParam>
     */
    public function getExerciseTypeParams(): Collection
    {
        return $this->exerciseTypeParams;
    }

    public function addExerciseTypeParam(ExerciseTypeParam $exerciseTypeParam): self
    {
        if (!$this->exerciseTypeParams->contains($exerciseTypeParam)) {
            $this->exerciseTypeParams->add($exerciseTypeParam);
            $exerciseTypeParam->setExerciseType($this);
        }

        return $this;
    }

    public function removeExerciseTypeParam(ExerciseTypeParam $exerciseTypeParam): self
    {
        if ($this->exerciseTypeParams->removeElement($exerciseTypeParam)) {
            // set the owning side to null (unless already changed)
            if ($exerciseTypeParam->getExerciseType() === $this) {
                $exerciseTypeParam->setExerciseType(null);
            }
        }

        return $this;
    }
}
