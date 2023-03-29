<?php

namespace App\Entity;

use App\Repository\FigurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FigurantRepository::class)]
class Figurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\OneToMany(mappedBy: 'figurant', targetEntity: Exercise::class, orphanRemoval: true)]
    private Collection $exercises;

    #[ORM\Column]
    private ?int $birthYear = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column(length: 255)]
    private ?string $occupation = null;

    #[ORM\Column(length: 255)]
    private ?string $sittingTimePerDay = null;

    #[ORM\Column(length: 255)]
    private ?string $activeHoursPerWeek = null;

    #[ORM\Column(length: 255)]
    private ?string $stretchingFrequency = null;

    #[ORM\Column]
    private ?int $weight = null;

    #[ORM\Column]
    private ?int $height = null;

    public function __construct()
    {
        $this->exercises = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * @return Collection<int, Exercise>
     */
    public function getExercises(): Collection
    {
        return $this->exercises;
    }

    public function addExercise(Exercise $exercise): self
    {
        if (!$this->exercises->contains($exercise)) {
            $this->exercises->add($exercise);
            $exercise->setFigurant($this);
        }

        return $this;
    }

    public function removeExercise(Exercise $exercise): self
    {
        if ($this->exercises->removeElement($exercise)) {
            // set the owning side to null (unless already changed)
            if ($exercise->getFigurant() === $this) {
                $exercise->setFigurant(null);
            }
        }

        return $this;
    }

    public function getBirthYear(): ?int
    {
        return $this->birthYear;
    }

    public function setBirthYear(int $birthYear): self
    {
        $this->birthYear = $birthYear;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getOccupation(): ?string
    {
        return $this->occupation;
    }

    public function setOccupation(string $occupation): self
    {
        $this->occupation = $occupation;

        return $this;
    }

    public function getSittingTimePerDay(): ?string
    {
        return $this->sittingTimePerDay;
    }

    public function setSittingTimePerDay(string $sittingTimePerDay): self
    {
        $this->sittingTimePerDay = $sittingTimePerDay;

        return $this;
    }

    public function getActiveHoursPerWeek(): ?string
    {
        return $this->activeHoursPerWeek;
    }

    public function setActiveHoursPerWeek(string $activeHoursPerWeek): self
    {
        $this->activeHoursPerWeek = $activeHoursPerWeek;

        return $this;
    }

    public function getStretchingFrequency(): ?string
    {
        return $this->stretchingFrequency;
    }

    public function setStretchingFrequency(string $stretchingFrequency): self
    {
        $this->stretchingFrequency = $stretchingFrequency;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }
}
