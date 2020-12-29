<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Tests\Entity\UserEntityTest;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min=8,
     *     max=40
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="date")
     * @Assert\LessThanOrEqual("-13 years")
     */
    private $dob;

    /**
     * @ORM\OneToOne(targetEntity=Todolist::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $todolist;

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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getDob(): ?\DateTimeInterface
    {
        return $this->dob;
    }

    public function setDob(\DateTimeInterface $dob): self
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * @see UserEntityTest::testYoungerThan13YearsOld()
     */
    public function isValid()
    {
        // In Symfony, it's recommended to use annotation to test properties
        // But we can test our entity with phpunit
    }

    public function getTodolist(): ?Todolist
    {
        return $this->todolist;
    }

    public function setTodolist(Todolist $todolist): self
    {
        // set the owning side of the relation if necessary
        if ($todolist->getUser() !== $this) {
            $todolist->setUser($this);
        }

        $this->todolist = $todolist;

        return $this;
    }
}
