<?php

namespace App\EventSubscriber;

use App\Entity\MediaObject;
use App\Interfaces\ImageInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $mediaPath;

    public function __construct(ParameterBagInterface $bag)
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
        /** @var ImageInterface $data */
        $data = $event->getData();
        if (!$data instanceof ImageInterface) {
            return;
        }

        $form = $event->getForm();

        $image = $form->get('image')->getData();
        if (!$image instanceof UploadedFile) {
            return;
        }

        $media = new MediaObject();
        $media->setFile($image);
        $data->setImage($media);
    }
}
