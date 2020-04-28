<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Provider;
use App\EventSubscriber\ImageFormSubscriber;
use App\Repository\ProviderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Product $product */
        $product = $options['data'];
        $builder
            ->add('name', TextType::class, ['label' => 'product.name'])
            ->add('description', TextareaType::class, ['label' => 'product.description'])
            ->add('image',FileType::class,[
                'label'=>'image.principal',
                'mapped'=>false,
                'required'=> false,
                'constraints' => [new Image()]
            ])
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
                'query_builder' => static function (ProviderRepository $repository) use ($product) {
                    return $repository->findByCompany($product->getCompany());
                },
                'label' => 'product.providers',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('submit', SubmitType::class, ['label' => 'save'])
            ->add('submit', SubmitType::class, ['label' => 'save'])
            ->addEventListener(FormEvents::POST_SUBMIT,[ImageFormSubscriber::class,'postSubmit'])
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
