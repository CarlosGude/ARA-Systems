<?php

namespace App\Form;

use App\Entity\Client;
use App\EventSubscriber\ImageFormSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'client.name'])
            ->add('identification', TextType::class, ['label' => 'client.identification'])
            ->add('image', FileType::class, [
                'label' => 'client.image',
                'mapped' => false,
                'required' => false,
                'constraints' => [new Image()],
            ])
            ->add('email', EmailType::class, ['label' => 'client.email'])
            ->add('address', TextType::class, ['label' => 'client.address'])
            ->add('submit', SubmitType::class, ['label' => 'save'])
            ->addEventListener(FormEvents::POST_SUBMIT, [ImageFormSubscriber::class, 'postSubmit'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
