<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\PurchaseLine;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var PurchaseLine $line */
        $line = $options['data'];

        $builder
            ->add('product', EntityType::class, [
                'label' => 'purchase.product',
                'class' => Product::class,
                'attr' => ['class' => 'select'],
                'query_builder' => static function (ProductRepository $repository) use ($line) {
                    return $repository->findByCompany($line->getCompany());
                },
            ])
            ->add('submit', SubmitType::class, ['label' => 'save'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseLine::class,
        ]);
    }
}
