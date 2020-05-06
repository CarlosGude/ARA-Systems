<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Company;
use App\Entity\User;
use App\Security\AbstractUserRoles;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name', TextType::class, ['label' => 'category.name'])
            ->add('description', TextareaType::class, ['label' => 'category.description','required'=>false])
            ->add('tax', ChoiceType::class, [
                'label' => 'category.tax',
                'choices' => $this->getTaxes(),
                'placeholder' => 'category.taxes',
                'required' => true,
            ])
            ->add('minStock', IntegerType::class, [
                'label' => 'category.minStock',
                'required' => true,
                'attr' => ['min' => 1],
            ])
            ->add('maxStock', IntegerType::class, [
                'label' => 'category.maxStock',
                'required' => true,
                'attr' => ['min' => 1],
            ])
            ->add('submit', SubmitType::class, ['label' => 'save'])

        ;
    }

    private function getTaxes(): array
    {
        return [
          Category::IVA_8.'%' => Category::IVA_8,
          Category::IVA_10.'%' => Category::IVA_10,
          Category::IVA_21.'%' => Category::IVA_21,
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
