<?php

namespace App\Entity;

use App\Repository\FacultyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FacultyRepository::class)
 */
class Faculty
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="faculty")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Classroom::class, mappedBy="faculty")
     */
    private $classrooms;

    /**
     * @ORM\OneToMany(targetEntity=Subject::class, mappedBy="faculty", orphanRemoval=true)
     */
    private $subjects;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $short;

    /**
     * @ORM\OneToMany(targetEntity=Semester::class, mappedBy="faculty", orphanRemoval=true)
     */
    private $semesters;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->classrooms = new ArrayCollection();
        $this->subjects = new ArrayCollection();
        $this->semesters = new ArrayCollection();
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
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setFaculty($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getFaculty() === $this) {
                $user->setFaculty(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Classroom[]
     */
    public function getClassrooms(): Collection
    {
        return $this->classrooms;
    }

    public function addClassroom(Classroom $classroom): self
    {
        if (!$this->classrooms->contains($classroom)) {
            $this->classrooms[] = $classroom;
            $classroom->setFaculty($this);
        }

        return $this;
    }

    public function removeClassroom(Classroom $classroom): self
    {
        if ($this->classrooms->removeElement($classroom)) {
            // set the owning side to null (unless already changed)
            if ($classroom->getFaculty() === $this) {
                $classroom->setFaculty(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Subject[]
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): self
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects[] = $subject;
            $subject->setFaculty($this);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): self
    {
        if ($this->subjects->removeElement($subject)) {
            // set the owning side to null (unless already changed)
            if ($subject->getFaculty() === $this) {
                $subject->setFaculty(null);
            }
        }

        return $this;
    }

    public function getShort(): ?string
    {
        return $this->short;
    }

    public function setShort(string $short): self
    {
        $this->short = $short;

        return $this;
    }

    /**
     * @return Collection|Semester[]
     */
    public function getSemesters(): Collection
    {
        return $this->semesters;
    }

    public function addSemester(Semester $semester): self
    {
        if (!$this->semesters->contains($semester)) {
            $this->semesters[] = $semester;
            $semester->setFaculty($this);
        }

        return $this;
    }

    public function removeSemester(Semester $semester): self
    {
        if ($this->semesters->removeElement($semester)) {
            // set the owning side to null (unless already changed)
            if ($semester->getFaculty() === $this) {
                $semester->setFaculty(null);
            }
        }

        return $this;
    }
}
