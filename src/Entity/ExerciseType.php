<?php

namespace App\Entity;

use App\Repository\ExerciseTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $instructionVideo = null;

    #[ORM\Column(length: 255)]
    private ?string $defaultVideoView = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column]
    private ?bool $deleted = false;

    #[ORM\Column]
    private ?bool $enabled = false;

    #[ORM\ManyToMany(targetEntity: ExerciseTypeCategory::class, inversedBy: 'exerciseTypes')]
    private Collection $categories;

    public function __construct()
    {
        $this->exerciseTypeParams = new ArrayCollection();
        $this->categories = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getInstructionVideo(): ?string
    {
        return $this->instructionVideo;
    }

    public function setInstructionVideo(string $instructionVideo): self
    {
        $this->instructionVideo = $instructionVideo;

        return $this;
    }

    public function getDefaultVideoView(): ?string
    {
        return $this->defaultVideoView;
    }

    public function setDefaultVideoView(string $defaultVideoView): self
    {
        $this->defaultVideoView = $defaultVideoView;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

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

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return Collection<int, ExerciseTypeCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(ExerciseTypeCategory $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(ExerciseTypeCategory $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
