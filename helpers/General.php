<?php
/**
 * Created by PhpStorm.
 */

namespace Signa\Helpers;

use Signa\Models\Invoices;
use Signa\Models\Organisations;

class General
{
    public static function generateInvoiceNumber($lab_id){

        $invoiceNumber = 0;

        $limitInvoices = 9999999;
        $checkInvoiceSequence = Organisations::findFirst("id = ".$lab_id." AND invoice_sequence IS NOT NULL");
        $totalInvoices = count(Invoices::find("seller_id = ".$lab_id));

        if($checkInvoiceSequence != false){
            $invoiceNumber = ($checkInvoiceSequence->getInvoiceSequence() + $totalInvoices) + 1;
        }
        else {
            $invoiceNumber = $totalInvoices + 1;
        }

        return $invoiceNumber;
    }

    public static function getInvoiceValues(Invoices $invoice)
    {
        $invoiceValuesArr = array('subtotal' => 0, 'btw' => array(), 'grandtotal' => 0);
        $records = $invoice->Records;

        foreach($records as $record)
        {
            $invoiceValuesArr['subtotal'] += $record->getPriceWithoutTax() * $record->getAmount();
            $invoiceValuesArr['grandtotal'] += $record->getPrice() * $record->getAmount();
            $invoiceValuesArr['btw'][(int)$record->getTax()] += round($record->getPriceWithoutTax() * ($record->getTax() / 100) * $record->getAmount(), 2);
        }

        return $invoiceValuesArr;
    }

    public static function randomString($length = 10)
    {
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $str = "";

        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $str;
    }

    public static function clearDirectory($dir)
    {
        $files = glob($dir.'/*');
        foreach($files as $file){
            if(is_file($file))
                unlink($file);
        }
    }

    /**
     * @param $string
     * @return null|string|string[]
     */
    public static function cleanString($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\.\-]/', '', $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }

    /**
     * @param string $imagePath
     * @param int $maxWH
     * @return mixed|bool
     */
    public static function resizeImage($imagePath, $maxWH = 800)
    {
        $image = new \Phalcon\Image\Adapter\Imagick($imagePath);
        $image->resize($maxWH, $maxWH);

        return $image->save();
    }
}