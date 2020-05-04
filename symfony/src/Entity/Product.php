<?php

namespace App\Entity;

use App\Interfaces\EntityInterface;
use App\Interfaces\ImageInterface;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Product implements EntityInterface, ImageInterface
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
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

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
     * @var MediaObject|null
     *
     * @ORM\ManyToOne(targetEntity=MediaObject::class,cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    public $image;

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
    private $tax;

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
     * @ORM\Column(type="float", nullable=false)
     */
    private $price = 0;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Provider", inversedBy="products")
     */
    private $providers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\MediaObject", inversedBy="products")
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PurchaseLine", mappedBy="product")
     */
    private $purchaseLines;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $productLength;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $productHeight;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $productWidth;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $kilograms;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @ORM\Column(type="integer")
     */
    private $reference;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->providers = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->purchaseLines = new ArrayCollection();
    }

    public function getIvas(): array
    {
        return [
            self::IVA_8,
            self::IVA_10,
            self::IVA_21,
            null,
        ];
    }

    public function __toString()
    {
        return $this->getName() ?? '';
    }

    /**
     * @ORM\PreUpdate
     */
    public function updatedAt(): Product
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    public function getId():? string
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

    public function setUser(User $user): EntityInterface
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

    public function getCategory(): ? Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

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

    /**
     * @return mixed
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @param mixed $tax
     */
    public function setTax($tax): Product
    {
        $this->tax = $tax;
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
     */
    public function setMinStock($minStock): Product
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
     */
    public function setMaxStock($maxStock): Product
    {
        $this->maxStock = $maxStock;
        return $this;
    }

    public function getStockAct(): int
    {
        return $this->stockAct;
    }

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

    /**
     * @return Collection|MediaObject[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(MediaObject $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
        }

        return $this;
    }

    public function removeImage(MediaObject $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
        }

        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): Product
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return Collection|PurchaseLine[]
     */
    public function getPurchaseLines(): Collection
    {
        return $this->purchaseLines;
    }

    public function addPurchaseLine(PurchaseLine $purchaseLine): self
    {
        if (!$this->purchaseLines->contains($purchaseLine)) {
            $this->purchaseLines[] = $purchaseLine;
            $purchaseLine->setProduct($this);
        }

        return $this;
    }

    public function removePurchaseLine(PurchaseLine $purchaseLine): self
    {
        if ($this->purchaseLines->contains($purchaseLine)) {
            $this->purchaseLines->removeElement($purchaseLine);
            // set the owning side to null (unless already changed)
            if ($purchaseLine->getProduct() === $this) {
                $purchaseLine->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return MediaObject|null
     */
    public function getImage(): ?MediaObject
    {
        return $this->image;
    }

    /**
     * @param MediaObject|null $image
     * @return Product
     */
    public function setImage(?MediaObject $image): ImageInterface
    {
        $this->image = $image;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getProductLength(): ?float
    {
        return $this->productLength;
    }

    public function setProductLength(float $productLength): self
    {
        $this->productLength = $productLength;

        return $this;
    }

    public function getProductHeight(): ?float
    {
        return $this->productHeight;
    }

    public function setProductHeight(?float $productHeight): self
    {
        $this->productHeight = $productHeight;

        return $this;
    }

    public function getProductWidth(): ?float
    {
        return $this->productWidth;
    }

    public function setProductWidth(?float $productWidth): self
    {
        $this->productWidth = $productWidth;

        return $this;
    }

    public function getKilograms(): ?float
    {
        return $this->kilograms;
    }

    public function setKilograms(?float $kilograms): self
    {
        $this->kilograms = $kilograms;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getReference(): ?int
    {
        return $this->reference;
    }

    public function setReference(int $reference): self
    {
        $this->reference = $reference;

        return $this;
    }


}
