<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PurchaseRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Purchase
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_INCOMING = 'incoming';
    public const STATUS_SUCCESS = 'success';
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $reference;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provider", inversedBy="purchases")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provider;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="purchases")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\Column(type="float")
     */
    private $total = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $taxes = 0;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="purchases")
     */
    private $user;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getStatuses(): array
    {
        return [
            self::STATUS_SUCCESS,
            self::STATUS_INCOMING,
            self::STATUS_PENDING,
        ];
    }

    /**
     * @ORM\PreUpdate
     */
    public function updatedAt(): Purchase
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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getTaxes(): ?float
    {
        return $this->taxes;
    }

    public function setTaxes(float $taxes): self
    {
        $this->taxes = $taxes;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
