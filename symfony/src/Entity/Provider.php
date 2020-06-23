<?php

namespace App\Entity;

use App\Interfaces\EntityInterface;
use App\Interfaces\ImageInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProviderRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Provider implements EntityInterface, ImageInterface
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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @var MediaObject|null
     *
     * @ORM\ManyToOne(targetEntity=MediaObject::class,cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    public $image;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="providers")
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Purchase", mappedBy="provider")
     */
    private $purchases;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PurchaseLine", mappedBy="provider")
     */
    private $purchaseLines;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="providers")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductProvider", mappedBy="provider", cascade={"remove"})
     */
    private $productProviders;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->purchases = new ArrayCollection();
        $this->purchaseLines = new ArrayCollection();
        $this->productProviders = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName() ?? '';
    }

    /**
     * @ORM\PreUpdate
     */
    public function updatedAt(): Provider
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

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): Provider
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): Provider
    {
        $this->updatedAt = $updatedAt;
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

    public function getImage(): ?MediaObject
    {
        return $this->image;
    }

    public function setImage(?MediaObject $image): ImageInterface
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return Collection|Purchase[]
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): self
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases[] = $purchase;
            $purchase->setProvider($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): self
    {
        if ($this->purchases->contains($purchase)) {
            $this->purchases->removeElement($purchase);
            // set the owning side to null (unless already changed)
            if ($purchase->getProvider() === $this) {
                $purchase->setProvider(null);
            }
        }

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
            $purchaseLine->setProvider($this);
        }

        return $this;
    }

    public function removePurchaseLine(PurchaseLine $purchaseLine): self
    {
        if ($this->purchaseLines->contains($purchaseLine)) {
            $this->purchaseLines->removeElement($purchaseLine);
            // set the owning side to null (unless already changed)
            if ($purchaseLine->getProvider() === $this) {
                $purchaseLine->setProvider(null);
            }
        }

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return Collection|ProductProvider[]
     */
    public function getProductProviders(): Collection
    {
        return $this->productProviders;
    }

    public function addProductProvider(ProductProvider $productProvider): self
    {
        if (!$this->productProviders->contains($productProvider)) {
            $this->productProviders[] = $productProvider;
            $productProvider->setProvider($this);
        }

        return $this;
    }

    public function removeProductProvider(ProductProvider $productProvider): self
    {
        if ($this->productProviders->contains($productProvider)) {
            $this->productProviders->removeElement($productProvider);
            // set the owning side to null (unless already changed)
            if ($productProvider->getProvider() === $this) {
                $productProvider->setProvider(null);
            }
        }

        return $this;
    }
}
