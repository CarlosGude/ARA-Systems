<?php


namespace App\Tests\Unitary;



use App\Entity\Color;
use App\Entity\Company;
use App\Entity\Size;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SizeTest extends WebTestCase
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
    public function testCreateASizeAndAddReference(): void
    {
        /** @var Company $company */
        $company = $this->manager->getRepository(Company::class)->findOneBy(['name' => 'The Company']);
        $sizes = count($this->manager->getRepository(Size::class)->findBy(['company' => $company]));

        $size = new Size();
        $size->setCompany($company)->setName('The Color')->setType(Size::SIZE_TYPE_CLOTHING_SIZE);

        $this->manager->persist($size);
        $this->manager->flush();

        $this->manager->refresh($size);

        $reference = str_pad($sizes, 2, '0', STR_PAD_LEFT);

        self::assertEquals($reference, $size->getReference());

    }
}