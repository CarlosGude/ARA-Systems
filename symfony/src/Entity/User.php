<?php


namespace App\Entity;


use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

class User implements UserInterface
{
    /** @var string */
    public const ROLE_GOD = 'ROLE_GOD';

    /** @var string */
    public const ROLE_USER = 'ROLE_USER';

    /**@var string */
    private $id;

    /**@var string */
    private $name;

    /**
     * @var string
     * @Assert\Email()
     */

    private $email;

    /** @var string */
    private $password;

    /** @var array */
    private $roles;

    /** @var string */
    private $salt;

    /** @var DateTime */
    private $createdAt;

    /** @var DateTime */
    private $updatedAt;

    public function __construct()
    {
        $this->roles = [self::ROLE_USER];
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->salt = uniqid(mt_rand(), true);
    }

    public function updatedAt()
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return User
     */
    public function setCreatedAt(DateTime $createdAt): User
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return User
     */
    public function setUpdatedAt(DateTime $updatedAt): User
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     * @return User
     */
    public function setSalt(string $salt): User
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}