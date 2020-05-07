<?php

namespace App\Tests\Front\Roles\RoleGod;

use App\Entity\Category;
use App\Entity\Product;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class CatalogManagementTest extends BaseTest
{
    public function testShowCatalog(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $client->request('GET', $this->generatePath('front_list',['entity'=>'product','view'=>'catalog']));

        self::assertResponseIsSuccessful();

    }

    public function testShowProduct(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        /** @var Product $categoryToEdit */
        $productToShow = $this->getRepository(Product::class)->findOneBy([
            'name' => 'Product 1',
            'company' => $this->getCompany('Another Company'),
        ]);
        $client->request('GET', $this->generatePath('front_list',['entity'=>'product','view'=>'catalog']));

        $url = $this->generatePath('front_edit', [
            'entity' => 'product',
            'id' => $productToShow->getId(),
            'view'=>'show'
        ]);
        $client->request('GET',$url);

        self::assertResponseIsSuccessful();
    }

}
