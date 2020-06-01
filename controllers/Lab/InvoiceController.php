<?php

namespace Signa\Controllers\Lab;

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

        $invoicesBulk = InvoicesBulk::find("created_by = '".$this->currentUser->getId()."' AND deleted_at IS NULL");
        $labDentists = LabDentists::find('lab_id = '.$this->currentUser->getOrganisationId());
        $dentist = [];
        $bulk = [];

        foreach ($invoicesBulk as $b){

            $bulk[$b->id] = $b->toArray();
            $bulk[$b->id]['invoices'] = $b->Invoices->toArray();
            $bulk[$b->id]['total_invoices'] = count($bulk[$b->id]['invoices']);
            $bulk[$b->id]['first_invoice'] = null;

            for ($i=0;$i<=$bulk[$b->id]['total_invoices'];$i++) {

                if ($i == 0) {
                    $bulk[$b->id]['first_invoice'] = $bulk[$b->id]['invoices'][$i];
                }
                elseif ($i == $bulk[$b->id]['total_invoices'] - 1) {

                    $bulk[$b->id]['last_invoice'] = $bulk[$b->id]['invoices'][$i];
                }
            }
        }

        foreach ($labDentists as $d){

            $dentist[$d->dentist_id] = $d->toArray();
            $dentist[$d->dentist_id]['dentist_data'] = $d->Dentist->toArray();
            $dentist[$d->dentist_id]['dentist_data']['country'] = $d->Dentist->country->code;
        }

        $this->assets->collection('footer')
            ->addJs("js/app/invoices.js");

        $this->view->dentists = $dentist;
        $this->view->invoicesBulk = $bulk;
    }

    public function addAction(){

        if($this->request->isAjax()){

            $this->view->disable();

            $post = $this->request->getPost();

            $createBulk = new InvoicesBulk();
            $createBulk->setLabId($this->currentUser->getOrganisationId());
            $createBulk->setBulkStatus('concept');
            $createBulk->setDate(date('Y-m-d', strtotime($post['date'])));
            $createBulk->setStartPeriod(date('Y-m-d', strtotime($post['start_date'])));
            $createBulk->setEndPeriod(date('Y-m-d', strtotime($post['end_date'])));
            $createBulk->save();

            $checkSave = false;

            foreach ($post['invoice_clients'] as $k => $v){

                $deliveryNotes = DeliveryNotes::find("created_at >='".date('Y-m-d H:i:s', strtotime($post['start_date']))."' AND created_at <='".date('Y-m-d H:i:s', strtotime($post['end_date']))."' AND invoice_id IS NULL AND order_dentist_id = '".$v."'");

                if(count($deliveryNotes) > 0){

                    foreach ($deliveryNotes as $dn){

                        $invoice = new Invoices();
                        $invoice->setDate($post['date']);
                        $invoice->setClientId($v);
                        $invoice->setBulkId($createBulk->getId());
                        $invoice->setSellerId($this->currentUser->getOrganisationId());
                        $invoice->setInvoiceType('lab');
                        $invoice->setInvoiceStatus('concept');
                        $invoice->save();

                        $dn->setInvoiceId($invoice->getId());
                        $dn->save();

                        $this->generateInvoice($invoice->getId(), 'concept');
                    }
                    $checkSave = true;
                }
            }

            if($checkSave == true){

                $message = [
                    'type' => 'success',
                    'content' => Trans::make('New invoices have been added')
                ];
            }
            else {
                $message = [
                    'type' => 'error',
                    'content' => Trans::make('Error while generating invoices')
                ];
            }
            return json_encode($message);
        }
    }

    public function deletebulkAction($id){

        $invoiceBulk = InvoicesBulk::findFirst($id);
        $invoiceBulk->softDelete();

        foreach ($invoiceBulk->Invoices as $invoice){

            $deliveryNote = DeliveryNotes::findFirst('invoice_id ='.$invoice->getId());
            $deliveryNote->setInvoiceId(NULL);
            $deliveryNote->save();
            $invoice->softDelete();
        }

        $message = [
            'type' => 'success',
            'content' => Trans::make('Invoice bulk has been deleted.')
        ];
        $this->session->set('message', $message);
        $this->response->redirect('/lab/invoice/');
        $this->view->disable();
        return;
    }

    public function processbulkAction($id){

        $invoiceBulk = InvoicesBulk::findFirst($id);

        foreach ($invoiceBulk->Invoices as $invoice){

            $invoice->setNumber(General::generateInvoiceNumber($this->currentUser->getOrganisationId()));
            $invoice->setInvoiceStatus('confirmed');
            $invoice->save();
        }
        $invoiceBulk->setBulkStatus('processed');
        $invoiceBulk->save();

        if($invoiceBulk->save()){
            $message = [
                'type' => 'success',
                'content' => Trans::make('Invoice bulk has been processed')
            ];
        }
        else {
            $message = [
                'type' => 'error',
                'content' => Trans::make('Error while processing invoice bulk')
            ];
        }

        $this->session->set('message', $message);
        $this->response->redirect('/lab/invoice/');
        $this->view->disable();
        return;
    }

    public function downloadzipAction($id){

        return $this->zipInvoices($id);
    }

    private function zipInvoices($id){

        $invoiceBulk = InvoicesBulk::findFirst($id);

        $zip = new ZipArchive();
        $zipName = $this->config->application->bulkInvoicesDir."bulk_".$id.".zip";
        $fileName = basename($zipName);

        if(file_exists($zipName) == TRUE) {
            unlink ($zipName);
        }

        if ($zip->open($zipName, ZIPARCHIVE::CREATE) != TRUE) {
            die ("Could not open archive");
        }

        if($invoiceBulk->getBulkStatus() == 'concept'){

            foreach($invoiceBulk->Invoices as $invoice) {

                if($invoice->getInvoiceStatus() == 'concept'){

                    $path = $this->config->application->conceptInvoicesDir."concept_".$invoice->DeliveryNote->Order->code.".pdf";
                    $zip->addFile($path,"concept_".$invoice->DeliveryNote->Order->code.".pdf");
                }
            }

        }
        elseif($invoiceBulk->getBulkStatus() == 'processed'){

            foreach($invoiceBulk->Invoices as $invoice) {

                if($invoice->getInvoiceStatus() == 'confirmed'){

                    $path = $this->config->application->confirmedInvoicesDir."invoice_".$invoice->getNumber().'.pdf';
                    $zip->addFile($path, "invoice_".$invoice->getNumber().'.pdf');
                }
            }
        }
        $close = $zip->close();

        if($close){

            chmod($zipName, 0777);

            $message = [
                'type' => 'success',
                'content' => Trans::make('Zip file has been created')
            ];

            header("Pragma: public");
            header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=$fileName");
            header("Content-Length: " . filesize($zipName));
            header("Content-Transfer-Encoding: binary");

            readfile($zipName);
            exit();
        }
        else {
            $message = [
                'type' => 'error',
                'content' => Trans::make('Error while creating zip file')
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/lab/invoice/');
            $this->view->disable();
            return;
        }
    }

    private function generateInvoice($id, $type){

        $this->view->disable();

        $invoice = Invoices::findFirst('id = '.$id);

        $html = $this->view->getRender('pdf', 'invoice', [
            'invoice' => $invoice,
            'lab' => $invoice->Seller,
            'labLogo' => $invoice->Seller->getLogo(),
            'dentist' => $invoice->Client,
            'order' => $invoice->DeliveryNote->Order,
            'deliveryNote' => $invoice->DeliveryNote,
            'invoiceNumber' => ($type == 'concept') ? 'CONCEPT' : $invoice->getNumber()
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

        if($type == 'concept'){
            $mpdf->Output($this->config->application->conceptInvoicesDir."concept_".$invoice->DeliveryNote->Order->code.".pdf", "F");
            chmod($this->config->application->conceptInvoicesDir."concept_".$invoice->DeliveryNote->Order->code.".pdf", 0777);
        }
        else {
            $mpdf->Output($this->config->application->confirmedInvoicesDir."invoice_".$invoice->getNumber().'.pdf', "F");
            chmod($this->config->application->confirmedInvoicesDir."invoice_".$invoice->getNumber().'.pdf', 0777);
        }
    }
}
