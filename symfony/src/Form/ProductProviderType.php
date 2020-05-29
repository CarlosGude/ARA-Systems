<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductProvider;
use App\Entity\Provider;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductProviderType extends AbstractType
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProductProvider $productProvider */
        $productProvider= $options['data'];

        (!$productProvider->getId()
            && !$productProvider->getProduct()
            && $productProvider->setProduct($this->getProduct()));

        $builder
            ->add('provider',EntityType::class,[
                'class'=> Provider::class,
                'label' => 'product.provider',
                'attr' => ['class' => 'select'],
                'query_builder' => static function(ProviderRepository $providerRepository) use ($productProvider){
                    return $providerRepository->findByCompany($productProvider->getCompany());
                }
            ])
            ->add('price', MoneyType::class, ['label' => 'product.price'])
            ->add('submit',SubmitType::class,['label'=>'save'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductProvider::class,
        ]);
    }

    protected function getProduct():? Product
    {
        $product = $this->requestStack->getMasterRequest()->query->get('product');
        /** @var null|Product $product */
        $product = $this->entityManager->getRepository(Product::class)->find($product);

        return $product;
    }
}
