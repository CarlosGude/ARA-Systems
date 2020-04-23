<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Provider;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'product.name'])
            ->add('description', TextareaType::class, ['label' => 'product.description'])
            ->add('tax', ChoiceType::class, [
                'label' => 'category.tax',
                'choices' => $this->getTaxes(),
                'placeholder' => 'product.taxes',
                'required' => true,
            ])
            ->add('minStock', IntegerType::class, [
                'label' => 'product.minStock',
                'required' => true,
                'attr' => ['min' => 1],
            ])
            ->add('maxStock', IntegerType::class, [
                'label' => 'product.maxStock',
                'required' => true,
                'attr' => ['min' => 1],
            ])
            ->add('stockAct', IntegerType::class, [
                'label' => 'product.stockAct',
                'attr' => ['min' => 1],
            ])
            ->add('price', MoneyType::class, ['label' => 'product.price'])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => 'product.category',
                'required' => true,
            ])
            ->add('providers', EntityType::class, [
                'class' => Provider::class,
                'label' => 'product.providers',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('submit', SubmitType::class, ['label' => 'save'])
        ;
    }

    private function getTaxes(): array
    {
        return [
            Product::IVA_8.'%' => Product::IVA_8,
            Product::IVA_10.'%' => Product::IVA_10,
            Product::IVA_21.'%' => Product::IVA_21,
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
