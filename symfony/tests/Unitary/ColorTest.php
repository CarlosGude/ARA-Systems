<?php


namespace App\Tests\Unitary;



use App\Entity\Color;
use App\Entity\Company;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ColorTest extends WebTestCase
{
    /** @var EntityManager */
    private $manager;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->manager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testCreateAColorAndAddReference(): void
    {
        /** @var Company $company */
        $company = $this->manager->getRepository(Company::class)->findOneBy(['name' => 'The Company']);
        $colors = count($this->manager->getRepository(Color::class)->findBy(['company' => $company]));

        $color = new Color();
        $color->setCompany($company)->setName('The Color');

        $this->manager->persist($color);
        $this->manager->flush();

        $this->manager->refresh($color);

        $reference = str_pad($colors, 2, '0', STR_PAD_LEFT);


        self::assertEquals($reference, $color->getReference());

    }
}