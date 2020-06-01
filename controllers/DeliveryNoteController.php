<?php

namespace Signa\Controllers;

use Signa\Models\DentistOrder;
use Signa\Helpers\Translations;
use Signa\Models\Organisations;
use Signa\Models\DeliveryNotes;

class DeliveryNoteController extends ControllerBase
{
    public function indexAction()
    {
        die;
    }

    public function previewAction($orderCode){

        $this->generatePdf($orderCode, 'preview');
    }

    public function viewAction($orderCode){

        $this->generatePdf($orderCode, 'view');
    }

    private function generatePdf($code, $type){

        $this->view->disable();

        $order = DentistOrder::findFirst(
            array(
                'deleted_at IS NULL AND code = :code:',
                'bind' => array(
                    'code' => $code
                )
            )
        );

        // Delivery note for the order
        $deliveryNote = DeliveryNotes::findFirst('order_id = '.$order->getId());

        if($deliveryNote == false){

            $amountNotes = count(DeliveryNotes::find('lab_id ='.$this->currentUser->Organisation->getId()));

            $deliveryNote = new DeliveryNotes();
            $deliveryNote->setOrderId($order->getId());
            $deliveryNote->setOrderDentistId($order->Dentist->getId());
            $deliveryNote->setLabId($this->currentUser->Organisation->getId());
            $deliveryNote->setDeliveryNumber($amountNotes+1);
            $deliveryNote->setStatus('concept');
            $deliveryNote->save();
        }

        if($type == 'view'){

            $length = 8-strlen($deliveryNote->getDeliveryNumber());
            $prefixNumber = "";

            for($i=1;$i<=$length;$i++){

                if($i == 1){
                    $prefixNumber = "0";
                }
                else {
                    $prefixNumber .= "0";
                }
            }
        }
        $lab = Organisations::findFirstById($deliveryNote->getLabId());
        $dentist = Organisations::findFirstById($deliveryNote->getOrderDentistId());
        $html = $this->view->getRender('pdf', 'delivery_note', [
            'lab' => $lab,
            'labLogo' => $lab->getLogo(),
            'dentist' => $dentist,
            'order' => $order,
            'deliveryNumber' => ($type == 'view') ? $prefixNumber.$deliveryNote->getDeliveryNumber() : Translations::make('Concept')
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
        $mpdf->SetTitle(Translations::make('Delivery note'));
        $mpdf->SetAuthor("Signadens");
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($stylesheet1.$stylesheet2.$stylesheet3,1);
        $mpdf->WriteHTML($html, 2);

        if($type == 'view'){
            $mpdf->Output("delivery_note_order_".$order->getCode().'.pdf', "D");
        }
        else {
            $mpdf->Output("preview_delivery_note_order_".$order->getCode().'.pdf', "D");
        }
    }
}
