<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreateMediaObjectAction;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ApiResource(
 *     iri="http://schema.org/MediaObject",
 *     normalizationContext={
 *         "groups"={"media_object_read"}
 *     },
 *     collectionOperations={
 *         "post"={
 *             "controller"=CreateMediaObjectAction::class,
 *             "deserialize"=false,
 *             "access_control"="is_granted('ROLE_GOD')",
 *             "validation_groups"={"Default", "media_object_create"},
 *             "openapi_context"={
 *                 "requestBody"={
 *                     "content"={
 *                         "multipart/form-data"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "file"={
 *                                         "type"="string",
 *                                         "format"="binary"
 *                                     }
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *         },
 *         "get"
 *     },
 *     itemOperations={
 *         "get"
 *     }
 * )
 * @Vich\Uploadable
 */
class MediaObject implements \Serializable
{
    /**
     * @var string|null
     *
     * @ApiProperty(iri="http://schema.org/contentUrl")
     * @Groups({"media_object_read"})
     */
    public $contentUrl;
    /**
     * @var File|null
     *
     * @Assert\NotNull(groups={"media_object_create"})
     * @Vich\UploadableField(mapping="media_object", fileNameProperty="filePath")
     */
    public $file;
    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    public $filePath;
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductMediaObject", mappedBy="mediaObject")
     */
    private $productMediaObjects;

    public function __construct()
    {
        $this->productMediaObjects = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): MediaObject
    {
        $this->id = $id;
        return $this;
    }

    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    public function setContentUrl(?string $contentUrl): MediaObject
    {
        $this->contentUrl = $contentUrl;
        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): MediaObject
    {
        $this->file = $file;
        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): MediaObject
    {
        $this->filePath = $filePath;
        return $this;
    }

    /** @see \Serializable::serialize() */
    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->filePath,
        ]);
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
    }

    /**
     * @return Collection|ProductMediaObject[]
     */
    public function getProductMediaObjects(): Collection
    {
        return $this->productMediaObjects;
    }

    public function addProductMediaObject(ProductMediaObject $productMediaObject): self
    {
        if (!$this->productMediaObjects->contains($productMediaObject)) {
            $this->productMediaObjects[] = $productMediaObject;
            $productMediaObject->setMediaObject($this);
        }

        return $this;
    }

    public function removeProductMediaObject(ProductMediaObject $productMediaObject): self
    {
        if ($this->productMediaObjects->contains($productMediaObject)) {
            $this->productMediaObjects->removeElement($productMediaObject);
            // set the owning side to null (unless already changed)
            if ($productMediaObject->getMediaObject() === $this) {
                $productMediaObject->setMediaObject(null);
            }
        }

        return $this;
    }
}
