<?php

namespace App\Utils;

use Com\Tecnick\Barcode\Barcode;

class BarcodeGenerator
{
    /**
     * @param $code
     *
     * @throws \Com\Tecnick\Barcode\Exception
     * @throws \Com\Tecnick\Color\Exception
     */
    public function generate($code)
    {
        $barcode = new Barcode();

        $bobj = $barcode
            ->getBarcodeObj('CODABAR,H', $code, 200, 50, 'black')
            ->setBackgroundColor('white')
        ;

        return $bobj->getSvg();
    }
}
