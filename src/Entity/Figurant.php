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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nickname = null;

    private bool $publicVideoConfirmation;

    #[ORM\OneToMany(mappedBy: 'figurant', targetEntity: Exercise::class, orphanRemoval: true)]
    private Collection $exercises;

    #[ORM\Column]
    private ?string $age;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column(length: 255)]
    private ?string $occupation = null;

    #[ORM\Column(length: 255)]
    private ?string $sittingTimePerDay = null;

    #[ORM\Column(length: 255)]
    private ?string $activeHoursPerWeek = null;

    #[ORM\Column(length: 255)]
    private ?string $sportHoursPerWeek = null;

    #[ORM\Column(length: 255)]
    private ?string $stretchingFrequency = null;

    #[ORM\Column]
    private ?string $weight = null;

    #[ORM\Column]
    private ?string $height = null;

    public function __construct()
    {
        $this->exercises = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return string|null
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @param string|null $nickname
     */
    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    /**
     * @return bool
     */
    public function isPublicVideoConfirmation(): bool
    {
        return $this->publicVideoConfirmation;
    }

    /**
     * @param bool $publicVideoConfirmation
     */
    public function setPublicVideoConfirmation(bool $publicVideoConfirmation): void
    {
        $this->publicVideoConfirmation = $publicVideoConfirmation;
    }

    /**
     * @return string|null
     */
    public function getAge(): ?string
    {
        return $this->age;
    }

    /**
     * @param string|null $age
     */
    public function setAge(?string $age): void
    {
        $this->age = $age;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string|null $gender
     */
    public function setGender(?string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return string|null
     */
    public function getOccupation(): ?string
    {
        return $this->occupation;
    }

    /**
     * @param string|null $occupation
     */
    public function setOccupation(?string $occupation): void
    {
        $this->occupation = $occupation;
    }

    /**
     * @return string|null
     */
    public function getSittingTimePerDay(): ?string
    {
        return $this->sittingTimePerDay;
    }

    /**
     * @param string|null $sittingTimePerDay
     */
    public function setSittingTimePerDay(?string $sittingTimePerDay): void
    {
        $this->sittingTimePerDay = $sittingTimePerDay;
    }

    /**
     * @return string|null
     */
    public function getActiveHoursPerWeek(): ?string
    {
        return $this->activeHoursPerWeek;
    }

    /**
     * @param string|null $activeHoursPerWeek
     */
    public function setActiveHoursPerWeek(?string $activeHoursPerWeek): void
    {
        $this->activeHoursPerWeek = $activeHoursPerWeek;
    }

    /**
     * @return string|null
     */
    public function getSportHoursPerWeek(): ?string
    {
        return $this->sportHoursPerWeek;
    }

    /**
     * @param string|null $sportHoursPerWeek
     */
    public function setSportHoursPerWeek(?string $sportHoursPerWeek): void
    {
        $this->sportHoursPerWeek = $sportHoursPerWeek;
    }

    /**
     * @return string|null
     */
    public function getStretchingFrequency(): ?string
    {
        return $this->stretchingFrequency;
    }

    /**
     * @param string|null $stretchingFrequency
     */
    public function setStretchingFrequency(?string $stretchingFrequency): void
    {
        $this->stretchingFrequency = $stretchingFrequency;
    }

    /**
     * @return string|null
     */
    public function getWeight(): ?string
    {
        return $this->weight;
    }

    /**
     * @param string|null $weight
     */
    public function setWeight(?string $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return string|null
     */
    public function getHeight(): ?string
    {
        return $this->height;
    }

    /**
     * @param string|null $height
     */
    public function setHeight(?string $height): void
    {
        $this->height = $height;
    }

}
