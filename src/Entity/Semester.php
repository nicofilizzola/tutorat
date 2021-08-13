<?php

namespace App\Entity;

use App\Repository\SemesterRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SemesterRepository::class)
 */
class Semester
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Faculty::class, inversedBy="semesters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $faculty;

    public function getId(): ?int
    {
        return $this->id;
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
}
