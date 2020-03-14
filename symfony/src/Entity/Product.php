<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    public const IVA_8 = 8;
    public const IVA_10 = 10;
    public const IVA_21 = 21;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="products")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $iva;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $minStock;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $maxStock;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $stockAct = 0;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Provider", inversedBy="products")
     */
    private $providers;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->providers = new ArrayCollection();
    }

    public function getIvas()
    {
        return [
            self::IVA_8,
            self::IVA_10,
            self::IVA_21,
            null,
        ];
    }

    /**
     * @ORM\PrePersist
     */
    public function updatedAt()
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    public function getId(): ?string
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * @param mixed $iva
     * @return Product
     */
    public function setIva($iva)
    {
        $this->iva = $iva;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinStock()
    {
        return $this->minStock;
    }

    /**
     * @param mixed $minStock
     * @return Product
     */
    public function setMinStock($minStock)
    {
        $this->minStock = $minStock;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxStock()
    {
        return $this->maxStock;
    }

    /**
     * @param mixed $maxStock
     * @return Product
     */
    public function setMaxStock($maxStock)
    {
        $this->maxStock = $maxStock;
        return $this;
    }

    /**
     * @return int
     */
    public function getStockAct(): int
    {
        return $this->stockAct;
    }

    /**
     * @param int $stockAct
     * @return Product
     */
    public function setStockAct(int $stockAct): Product
    {
        $this->stockAct = $stockAct;
        return $this;
    }

    /**
     * @return Collection|Provider[]
     */
    public function getProviders(): Collection
    {
        return $this->providers;
    }

    public function addProvider(Provider $provider): self
    {
        if (!$this->providers->contains($provider)) {
            $this->providers[] = $provider;
        }

        return $this;
    }

    public function removeProvider(Provider $provider): self
    {
        if ($this->providers->contains($provider)) {
            $this->providers->removeElement($provider);
        }

        return $this;
    }
}
