<?php

namespace Signa\Controllers\Dentist;

use Signa\Models\Invoices;
use Signa\Models\InvoiceRecords;
use Signa\Helpers\General;
use Signa\Helpers\Translations as Trans;
use Signa\Models\Organisations;
use Signa\Models\LabDentists;
use Signa\Models\Countries;
use Signa\Models\DeliveryNotes;
use Signa\Models\InvoicesBulk;
use \ZipArchive as ZipArchive;

class InvoiceController extends InitController
{
    public function indexAction(){

        $this->view->invoices = Invoices::find("client_id = ".$this->currentUser->getOrganisationId()." AND invoice_status = 'confirmed'");
    }

    public function downloadAction($id){

        $this->generatePdf($id);
    }

    private function generatePdf($id){

        $this->view->disable();

        $invoice = Invoices::findFirst('id = '.$id);
        $labDentist = LabDentists::findFirst('dentist_id = '.$invoice->Client->getId().' AND lab_id = '.$invoice->Seller->getId());
        $paymentArrangement = [];

        foreach($invoice->DeliveryNote->Order->DentistOrderRecipe as $dor){

            $amount = ($dor->getPrice() * $labDentist->PaymentArrangement->getPercentage())/100;

            $paymentArrangement[$dor->id] = str_replace([
                '[discount_percentage]',
                '[discount_amount]',
                '[price_minus_discount]'
            ], [
                $labDentist->PaymentArrangement->getPercentage(),
                $amount,
                $dor->getPrice() - $amount,
            ], $labDentist->PaymentArrangement->getDescription());
        }

        $html = $this->view->getRender('pdf', 'invoice', [
            'invoice' => $invoice,
            'lab' => $invoice->Seller,
            'labLogo' => $invoice->Seller->getLogo(),
            'dentist' => $invoice->Client,
            'order' => $invoice->DeliveryNote->Order,
            'deliveryNote' => $invoice->DeliveryNote,
            'invoiceNumber' => $invoice->getNumber(),
            'paymentArrangement' => $paymentArrangement
        ]);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'tempDir' => $this->config->application->cacheDir,
            'setAutoTopMargin' => 'stretch'
        ]);

        $stylesheet1 = file_get_contents('css/pdf/bootstrap.css');
        $stylesheet2 = file_get_contents('css/pdf/bootstrap-theme.min.css');
        $stylesheet3 = file_get_contents('css/pdf/style.css');

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle(Trans::make('Invoice'));
        $mpdf->SetAuthor("Signadens");
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($stylesheet1.$stylesheet2.$stylesheet3,1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output("invoice_".$invoice->getNumber().'.pdf', "D");
    }
}
