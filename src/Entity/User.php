<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email"}, message="Cette adresse email est déjà prise.")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="integer")
     */
    private $year;

    /**
     * @ORM\Column(type="integer")
     */
    private $isValid;

    /**
     * @ORM\ManyToOne(targetEntity=Faculty::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=true)
     */
    private $faculty;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
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
        // $roles[] = 'ROLE_USER';

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
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getIsValid(): ?int
    {
        return $this->isValid;
    }

    public function setIsValid(int $isValid): self
    {
        $this->isValid = $isValid;

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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function __toString()
    {
        return ucfirst($this->firstName) . " " . ucfirst($this->lastName);
    }

    public function getTutorHours(SessionRepository $sessionRepository){
        if (in_array("ROLE_TUTOR", $this->getRoles()) && $this->isVerified() && $this->getIsValid() == 2){
            $tutorSessions = $sessionRepository->findBy([
                'tutor' => $this,
                'isValid' => true
            ]);
            $validatedSessionHours = 0;
            foreach ($tutorSessions as $session){
                if (!empty($session->getParticipants())){
                    switch ($session->getTimeFormat()){
                        case 1:
                            $addedTime = 0.5;
                            break;
                        case 2:
                            $addedTime = 0.75;
                            break;
                        case 3:
                            $addedTime = 1;
                            break;
                    }
                    $validatedSessionHours += $addedTime;
                }
            }
            $timeComponents = explode('.', number_format((float)$validatedSessionHours * 30 / 50, 2));
            if ($timeComponents[1] == 60){
                $timeComponents[0] = intval($timeComponents[0]) + 1;
                $timeComponents[1] = '00';
            }

            return $timeComponents[0] . 'h' . $timeComponents[1];
        } else {
            return;
        }
    }
}
