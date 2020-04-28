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
     * @param MediaObject|null $image
     * @return ImageInterface
     */
    public function setImage(?MediaObject $image): ImageInterface;
}