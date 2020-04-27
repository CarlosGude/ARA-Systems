<?php

namespace App\EventSubscriber;

use App\Entity\Company;
use App\Entity\MediaObject;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CompanyFormSubscriber implements EventSubscriberInterface
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
           FormEvents::POST_SUBMIT => 'postSubmit'
        ];
    }

    public static function postSubmit(FormEvent $event): void
    {
        /** @var Company $company */
        $company = $event->getData();

        $form = $event->getForm();

        $logo = $form->get('logo')->getData();
        if (!$logo instanceof UploadedFile){
            return;
        }

        $media = new MediaObject();
        $media->setFile($logo);
        $company->setImage($media);
    }

}
