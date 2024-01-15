<?php

namespace App\Entity;

use App\Repository\ExerciseTypeCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciseTypeCategoryRepository::class)]
class ExerciseTypeCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $deleted = false;

    #[ORM\ManyToMany(targetEntity: ExerciseType::class, mappedBy: 'categories')]
    private Collection $exerciseTypes;

    #[ORM\Column]
    private ?bool $global = null;

    public function __construct()
    {
        $this->exerciseTypes = new ArrayCollection();
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

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return Collection<int, ExerciseType>
     */
    public function getExerciseTypes(): Collection
    {
        return $this->exerciseTypes;
    }

    public function addExerciseType(ExerciseType $exerciseType): self
    {
        if (!$this->exerciseTypes->contains($exerciseType)) {
            $this->exerciseTypes->add($exerciseType);
            $exerciseType->addCategory($this);
        }

        return $this;
    }

    public function removeExerciseType(ExerciseType $exerciseType): self
    {
        if ($this->exerciseTypes->removeElement($exerciseType)) {
            $exerciseType->removeCategory($this);
        }

        return $this;
    }

    public function isGlobal(): ?bool
    {
        return $this->global;
    }

    public function setGlobal(bool $global): self
    {
        $this->global = $global;

        return $this;
    }
}
