<?php


namespace App\Tests\Unitary;


use App\Entity\Product;
use App\Entity\ProductProvider;
use App\Entity\Provider;
use App\Entity\Purchase;
use App\Entity\PurchaseLine;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PurchaseTest extends WebTestCase
{
    /** @var EntityManager */
    private $manager;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->manager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function createPurchase(string $email, string $provider): Purchase
    {
        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['email' => $email]);

        /** @var Provider $provider */
        $provider = $this->manager->getRepository(Provider::class)->findOneBy(['name' => $provider]);

        $purchase = new Purchase();

        $purchase
            ->setUser($user)
            ->setCompany($user->getCompany())
            ->setProvider($provider)
        ;

        for ($i=1;$i<=10;$i++){
            $product = $this->manager->getRepository(Product::class)->findOneBy([
                'name' => 'Product '.$i,
                'company' => $purchase->getCompany()
            ]);

            if(!$product){
                continue;
            }
            $price = random_int(1,($product->getPrice() - 1));

            $purchaseLine = new PurchaseLine();
            $purchaseLine
                ->setCompany($purchase->getCompany())
                ->setProduct($product)
                ->setPrice($price)
                ->setQuantity(random_int(1,10))
                ->setPurchase($purchase)
            ;

            $this->manager->persist($purchaseLine);
        }

        $this->manager->flush();

        return $purchase;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testChangeStatusToSuccessUpdateStockAndProvidersPrice(): void
    {
        $purchase = $this->createPurchase('carlos.sgude@gmail.com','The Provider');
        $this->manager->refresh($purchase);
        $stockBefore = [];
        foreach ($purchase->getPurchaseLines() as $line) {
            $product = $line->getProduct();
            $stockBefore[$product->getId()] = [
                'stock' => $product->getStockAct(),
                'quantity' => $line->getQuantity(),
            ];
        }

        $purchase->setStatus(Purchase::STATUS_SUCCESS);
        $this->manager->flush();
        $this->manager->refresh($purchase);

        $stockAfter = [];
        foreach ($purchase->getPurchaseLines() as $afterLine) {
            $product = $afterLine->getProduct();
            $this->manager->refresh($product);
            $stockAfter[$product->getId()] = ['stock' => $product->getStockAct()];
            $productProviders = $product->getProductProviders()->filter(function (ProductProvider $productProvider) use ($purchase) {
                return $productProvider->getProvider() === $purchase->getProvider();
            });

            self::assertEquals(1,$productProviders->count());
            /** @var ProductProvider $productProvider */
            $productProvider = $productProviders->first();
            self::assertEquals($afterLine->getPrice(),$productProvider->getPrice());
        }

        foreach ($stockAfter as $id => $stock) {
            self::assertEquals($stock['stock'], $stockBefore[$id]['stock'] + $stockBefore[$id]['quantity']);
        }
    }
}