<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column]
    private int $birthYear;

    #[ORM\Column(length: 255)]
    private string $country;

    #[ORM\Column(length: 255)]
    private string $completedEducationLevel;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $completedEducationUniversityName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $completedEducationFacultyName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $currentEducationLevel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $studyProgram = null;

    #[ORM\Column(nullable: true)]
    private ?int $academicYear = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $universityName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facultyName = null;

    #[ORM\Column(length: 255)]
    private ?string $fieldOfExperience = null;

    #[ORM\Column]
    private ?int $yearsOfExperience = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $courseList = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $favoriteMethod = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $currentJobTitle = null;

    #[ORM\Column]
    private ?bool $stayInTouch = null;

    #[ORM\Column]
    private ?bool $deleted = false;

    #[ORM\Column]
    private ?bool $evaluateGlobalCategories = true;

    #[ORM\ManyToMany(targetEntity: ExerciseTypeCategory::class)]
    private Collection $exerciseTypeCategories;

    public function __construct()
    {
        $this->exerciseTypeCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

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

    public function getYearsOfExperience(): ?int
    {
        return $this->yearsOfExperience;
    }

    public function setYearsOfExperience(int $yearsOfExperience): self
    {
        $this->yearsOfExperience = $yearsOfExperience;

        return $this;
    }

    public function getCurrentEducationLevel(): ?string
    {
        return $this->currentEducationLevel;
    }

    public function setCurrentEducationLevel(?string $currentEducationLevel): self
    {
        $this->currentEducationLevel = $currentEducationLevel;

        return $this;
    }

    public function getAcademicYear(): ?int
    {
        return $this->academicYear;
    }

    public function setAcademicYear(?int $academicYear): self
    {
        $this->academicYear = $academicYear;

        return $this;
    }

    public function getUniversityName(): ?string
    {
        return $this->universityName;
    }

    public function setUniversityName(?string $universityName): self
    {
        $this->universityName = $universityName;

        return $this;
    }

    public function getFacultyName(): ?string
    {
        return $this->facultyName;
    }

    public function setFacultyName(?string $facultyName): self
    {
        $this->facultyName = $facultyName;

        return $this;
    }

    public function getStudyProgram(): ?string
    {
        return $this->studyProgram;
    }

    public function setStudyProgram(?string $studyProgram): self
    {
        $this->studyProgram = $studyProgram;

        return $this;
    }

    public function getFieldOfExperience(): ?string
    {
        return $this->fieldOfExperience;
    }

    public function setFieldOfExperience(string $fieldOfExperience): self
    {
        $this->fieldOfExperience = $fieldOfExperience;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCourseList(): ?string
    {
        return $this->courseList;
    }

    public function setCourseList(?string $courseList): self
    {
        $this->courseList = $courseList;

        return $this;
    }

    public function getFavoriteMethod(): ?string
    {
        return $this->favoriteMethod;
    }

    public function setFavoriteMethod(?string $favoriteMethod): self
    {
        $this->favoriteMethod = $favoriteMethod;

        return $this;
    }

    public function isStayInTouch(): ?bool
    {
        return $this->stayInTouch;
    }

    public function setStayInTouch(bool $stayInTouch): self
    {
        $this->stayInTouch = $stayInTouch;

        return $this;
    }

    public function getCurrentJobTitle(): ?string
    {
        return $this->currentJobTitle;
    }

    public function setCurrentJobTitle(?string $currentJobTitle): self
    {
        $this->currentJobTitle = $currentJobTitle;

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
     * @return string
     */
    public function getCompletedEducationLevel(): string
    {
        return $this->completedEducationLevel;
    }

    /**
     * @param string $completedEducationLevel
     */
    public function setCompletedEducationLevel(string $completedEducationLevel): void
    {
        $this->completedEducationLevel = $completedEducationLevel;
    }

    /**
     * @return string|null
     */
    public function getCompletedEducationUniversityName(): ?string
    {
        return $this->completedEducationUniversityName;
    }

    /**
     * @param string|null $completedEducationUniversityName
     */
    public function setCompletedEducationUniversityName(?string $completedEducationUniversityName): void
    {
        $this->completedEducationUniversityName = $completedEducationUniversityName;
    }

    /**
     * @return string|null
     */
    public function getCompletedEducationFacultyName(): ?string
    {
        return $this->completedEducationFacultyName;
    }

    /**
     * @param string|null $completedEducationFacultyName
     */
    public function setCompletedEducationFacultyName(?string $completedEducationFacultyName): void
    {
        $this->completedEducationFacultyName = $completedEducationFacultyName;
    }

    public function isEvaluateGlobalCategories(): ?bool
    {
        return $this->evaluateGlobalCategories;
    }

    public function setEvaluateGlobalCategories(bool $evaluateGlobalCategories): self
    {
        $this->evaluateGlobalCategories = $evaluateGlobalCategories;

        return $this;
    }

    /**
     * @return Collection<int, ExerciseTypeCategory>
     */
    public function getExerciseTypeCategories(): Collection
    {
        return $this->exerciseTypeCategories;
    }

    public function addExerciseTypeCategory(ExerciseTypeCategory $exerciseTypeCategory): self
    {
        if (!$this->exerciseTypeCategories->contains($exerciseTypeCategory)) {
            $this->exerciseTypeCategories->add($exerciseTypeCategory);
        }

        return $this;
    }

    public function removeExerciseTypeCategory(ExerciseTypeCategory $exerciseTypeCategory): self
    {
        $this->exerciseTypeCategories->removeElement($exerciseTypeCategory);

        return $this;
    }



}
