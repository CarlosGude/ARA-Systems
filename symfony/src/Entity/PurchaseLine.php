<?php

namespace App\Entity;

use App\Interfaces\EntityInterface;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PurchaseLineRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PurchaseLine implements EntityInterface
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Purchase", inversedBy="purchaseLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $purchase;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="purchaseLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="purchaseLines")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="purchaseLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provider", inversedBy="purchaseLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provider;

    /**
     * @ORM\Column(type="integer")
     */
    private $tax = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity = 1;

    /**
     * @ORM\Column(type="float")
     */
    private $price = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function __toString(): string
    {
        $product = $this->getProduct();
        if ($product) {
            return $product->getName() ?? '';
        }

        return '';
    }

    /**
     * @ORM\PreUpdate
     */
    public function updatedAt(): self
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPurchase(): Purchase
    {
        return $this->purchase;
    }

    public function setPurchase(?Purchase $purchase): self
    {
        $this->purchase = $purchase;

        if ($purchase) {
            $this->setProvider($purchase->getProvider());
        }

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): EntityInterface
    {
        $this->user = $user;

        return $this;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function setProvider(Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getTotalWithoutTaxes(): float
    {
        return $this->getTotal() + ($this->getTotal() * ($this->getTax() / 100));
    }

    public function getTotal(): float
    {
        return $this->getPrice() * $this->getQuantity();
    }

    public function getTax(): int
    {
        return $this->tax;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function setTax(int $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * @return int
     */
    public function getStockAvailability(): ?int
    {
        if (!$this->getProduct()) {
            return null;
        }
        return $this->getProduct()->getStockAct();
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        $this
            ->setPrice($product->getPrice())
            ->setTax($product->getTax());

        return $this;
    }
}
