<?php


namespace App\Utils;


use Com\Tecnick\Barcode\Barcode;

class BarcodeGenerator
{
    /**
     * @param $code
     * @throws \Com\Tecnick\Barcode\Exception
     * @throws \Com\Tecnick\Color\Exception
     */
    public function generate($code)
    {
        $barcode = new Barcode();

        $bobj = $barcode->getBarcodeObj(
            'CODABAR,H',                     // barcode type and additional comma-separated parameters
            $code,         // data string to encode
            200,                             // bar width (use absolute or negative value as multiplication factor)
            50,                             // bar height (use absolute or negative value as multiplication factor)
            'black',                        // foreground color
            array(5,5,5,5)           // padding (use absolute or negative values as multiplication factors)
        )->setBackgroundColor('white');

        return $bobj->getHtmlDiv();
    }
}