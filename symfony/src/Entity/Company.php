<?php

namespace App\Entity;

use App\Interfaces\ImageInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Company implements ImageInterface
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
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @var MediaObject|null
     *
     * @ORM\ManyToOne(targetEntity=MediaObject::class,cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    public $image;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="company")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="company")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="company")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Provider", mappedBy="company")
     */
    private $providers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Purchase", mappedBy="company")
     */
    private $purchases;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PurchaseLine", mappedBy="company")
     */
    private $purchaseLines;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Client", mappedBy="company")
     */
    private $clients;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->categories = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->providers = new ArrayCollection();
        $this->purchases = new ArrayCollection();
        $this->purchaseLines = new ArrayCollection();
        $this->clients = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }

    /**
     * @ORM\PreUpdate
     */
    public function updatedAt(): Company
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): Company
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ? string
    {
        return $this->name;
    }

    public function setName(string $name): Company
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Company
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): Company
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): Company
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setCompany($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            // set the owning side to null (unless already changed)
            if ($category->getCompany() === $this) {
                $category->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCompany($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getCompany() === $this) {
                $product->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getCompany() === $this) {
                $user->setCompany(null);
            }
        }

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
            $provider->setCompany($this);
        }

        return $this;
    }

    public function removeProvider(Provider $provider): self
    {
        if ($this->providers->contains($provider)) {
            $this->providers->removeElement($provider);
            // set the owning side to null (unless already changed)
            if ($provider->getCompany() === $this) {
                $provider->setCompany(null);
            }
        }

        return $this;
    }

    public function getImage(): ?MediaObject
    {
        return $this->image;
    }

    public function setImage(?MediaObject $image): Company
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
            $purchase->setCompany($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): self
    {
        if ($this->purchases->contains($purchase)) {
            $this->purchases->removeElement($purchase);
            // set the owning side to null (unless already changed)
            if ($purchase->getCompany() === $this) {
                $purchase->setCompany(null);
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
            $purchaseLine->setCompany($this);
        }

        return $this;
    }

    public function removePurchaseLine(PurchaseLine $purchaseLine): self
    {
        if ($this->purchaseLines->contains($purchaseLine)) {
            $this->purchaseLines->removeElement($purchaseLine);
            // set the owning side to null (unless already changed)
            if ($purchaseLine->getCompany() === $this) {
                $purchaseLine->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Client[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->setCompany($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->contains($client)) {
            $this->clients->removeElement($client);
            // set the owning side to null (unless already changed)
            if ($client->getCompany() === $this) {
                $client->setCompany(null);
            }
        }

        return $this;
    }
}
