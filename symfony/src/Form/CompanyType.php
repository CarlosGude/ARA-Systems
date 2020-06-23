<?php

namespace App\Form;

use App\Entity\Company;
use App\EventSubscriber\ImageFormSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'company.name'])
            ->add('image', FileType::class, [
                'label' => 'company.logo',
                'mapped' => false,
                'required' => false,
                'constraints' => [new Image()],
            ])
            ->add('phone', TextType::class, ['label' => 'company.phone'])
            ->add('email', EmailType::class, ['label' => 'company.email'])
            ->add('address', TextType::class, ['label' => 'company.address'])
            ->add('cif', TextType::class, ['label' => 'company.cif'])
            ->add('description', TextareaType::class, ['label' => 'company.description', 'required' => false])
            ->add('submit', SubmitType::class, ['label' => 'save'])
            ->addEventListener(FormEvents::POST_SUBMIT, [ImageFormSubscriber::class, 'postSubmit'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
