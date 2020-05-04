<?php

namespace App\EventSubscriber;

use App\Entity\MediaObject;
use App\Entity\Product;
use App\Entity\ProductMediaObject;
use App\Interfaces\ImageInterface;
use App\Interfaces\MultipleImagesInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MultipleImageFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $mediaPath;


    public function __construct(ParameterBagInterface $bagr)
    {
        $this->mediaPath = $bag->get('kernel.project_dir').'/public/media';
    }

    public static function getSubscribedEvents(): array
    {
        return [
           FormEvents::POST_SUBMIT => 'postSubmit',
        ];
    }

    public static function postSubmit(FormEvent $event): void
    {
        /** @var ImageInterface $product */
        $product = $event->getData();
        if (!$product instanceof Product) {
            return;
        }

        $form = $event->getForm();

        $images = $form->get('images')->getData();
        if (empty($images)) {
            return;
        }

        foreach ($images as $image) {
            $product
                ->addProductMediaObject(
                    (new ProductMediaObject())
                        ->setProduct($product)
                        ->setMediaObject((new MediaObject())->setFile($image)
                    ));

        }

    }
}
