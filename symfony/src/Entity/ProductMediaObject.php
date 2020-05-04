<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductMediaObjectRepository")
 */
class ProductMediaObject
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productMediaObjects")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MediaObject", inversedBy="productMediaObjects",cascade={"persist"})
     */
    private $mediaObject;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getMediaObject(): ?MediaObject
    {
        return $this->mediaObject;
    }

    public function setMediaObject(?MediaObject $mediaObject): self
    {
        $this->mediaObject = $mediaObject;

        return $this;
    }

    public function setImage(?MediaObject $mediaObject): self
    {
        return $this->setMediaObject($mediaObject);
    }
}
