<?php

namespace App\Utils;

use App\Entity\Company;
use Com\Tecnick\Barcode\Barcode;
use Doctrine\ORM\EntityManagerInterface;

class TwigUtils
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $code
     *
     * @throws \Com\Tecnick\Barcode\Exception
     * @throws \Com\Tecnick\Color\Exception
     */
    public function barcode($code)
    {
        $barcode = new Barcode();

        $bobj = $barcode
            ->getBarcodeObj('CODABAR,H', $code, 200, 50, 'black')
            ->setBackgroundColor('white')
        ;

        return $bobj->getHtmlDiv();
    }

    public function getCompanies()
    {
        return $this->entityManager->getRepository(Company::class)->findAll();
    }
}
