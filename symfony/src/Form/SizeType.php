<?php

namespace App\Form;

use App\Entity\Size;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SizeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'color.name'])
            ->add('type', ChoiceType::class, [
                'label' => 'size.type',
                'choices' => $this->getTypes(),
                'placeholder' => 'size.typeChoose',
            ])
            ->add('submit', SubmitType::class, ['label' => 'save'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Size::class,
        ]);
    }

    protected function getTypes()
    {
        return [
            Size::SIZE_TYPE_LONG => Size::SIZE_TYPE_LONG,
            Size::SIZE_TYPE_CLOTHING_SIZE => Size::SIZE_TYPE_CLOTHING_SIZE,
        ];
    }
}
