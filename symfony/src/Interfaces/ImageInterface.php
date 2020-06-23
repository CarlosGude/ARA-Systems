<?php

namespace App\Interfaces;

use App\Entity\MediaObject;

interface ImageInterface
{
    /**
     * @return MediaObject|null
     */
    public function getImage(): ?MediaObject;

    /**
     * @return ImageInterface
     */
    public function setImage(?MediaObject $image): ImageInterface;
}
