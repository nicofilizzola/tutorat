<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubjectRepository::class)
 */
class Subject
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
    private $title;

    /**
     * @ORM\Column(type="integer")
     */
    private $semester;

    /**
     * @ORM\ManyToOne(targetEntity=Faculty::class, inversedBy="subjects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $faculty;

    /**
     * @ORM\OneToMany(targetEntity=Session::class, mappedBy="Subject", orphanRemoval=true)
     */
    private $sessions;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $short;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSemester(): ?int
    {
        return $this->semester;
    }

    public function setSemester(int $semester): self
    {
        $this->semester = $semester;

        return $this;
    }

    public function getFaculty(): ?Faculty
    {
        return $this->faculty;
    }

    public function setFaculty(?Faculty $faculty): self
    {
        $this->faculty = $faculty;

        return $this;
    }

    /**
     * @return Collection|Session[]
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->setSubject($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getSubject() === $this) {
                $session->setSubject(null);
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
}
