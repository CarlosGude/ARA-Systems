<?php

namespace App\Entity;

use App\Interfaces\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductProviderRepository")
 */
class ProductProvider implements EntityInterface
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productProviders")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provider", inversedBy="productProviders")
     */
    private $provider;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPreferedProvider = false;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="productProviders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="productProviders")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->getProduct()->getName();
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function getProductPrice(): float
    {
        if ($this->getProduct()){
            return $this->getProduct()->getPrice();
        }

        return 0;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIsPreferedProvider(): ?bool
    {
        return $this->isPreferedProvider;
    }

    public function setIsPreferedProvider(bool $isPreferedProvider): self
    {
        $this->isPreferedProvider = $isPreferedProvider;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return ProductProvider
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     * @return ProductProvider
     */
    public function setUpdatedAt($updatedAt)
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): EntityInterface
    {
        $this->user = $user;

        return $this;
    }


}
