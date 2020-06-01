<?php

namespace Signa\Controllers\Signadens;

use Signa\Models\Invoices;
use Signa\Models\InvoiceRecords;
use Signa\Helpers\General;
use Signa\Helpers\Translations as Trans;

class InvoiceController extends InitController
{
    public function indexAction(){

        $invoices = Invoices::find('deleted_by IS NULL');
        $this->view->invoices = $invoices;
    }

    public function addAction(){

        if($this->request->isPost()){

            $post = $this->request->getPost();

            $invoice = new Invoices();
            $saved = $invoice->saveData($post);

            if($saved){
                $message = [
                    'type' => 'success',
                    'content' => Trans::make('New invoice has been added.')
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/invoice/');
                $this->view->disable();
                return;
            }
            else {

                $messages = $invoice->getMessages();
                $errorsString = '';

                foreach ($messages as $message) {

                    $errorsString .= $message."</br>";
                }
                $tmessage = [
                    'type' => 'error',
                    'content' => $errorsString
                ];
                $this->session->set('message', $tmessage);
            }

            $this->response->redirect('/signadens/invoice/add');
            $this->view->disable();
            return;
        }
    }

    public function addrecordAction($id){

        if($this->request->isPost()){

            $post = $this->request->getPost();

            $record = new InvoiceRecords();
            $saved = $record->saveData($post);

            if($saved){
                $message = [
                    'type' => 'success',
                    'content' => 'New record has been added.'
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/invoice/edit/'.$id);
                $this->view->disable();
                return;
            }
            $message = [
                'type' => 'warning',
                'content' => 'New record cannot be added. Error corrupted'
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/signadens/invoice/addrecord/'.$id);
            $this->view->disable();
            return;
        }
        $this->view->invoiceId = $id;
    }

    public function editAction($id){

        $invoice = Invoices::findFirst($id);

        if($this->request->isPost()){

            $post = $this->request->getPost();

            $saved = $invoice->saveData($post);

            if($saved){
                $message = [
                    'type' => 'success',
                    'content' => Trans::make('New invoice has been added.')
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/invoice/');
                $this->view->disable();
                return;
            }
            $message = [
                'type' => 'warning',
                'content' => Trans::make('New invoice cannot be added. Error corrupted')
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/signadens/invoice/edit/'.$id);
            $this->view->disable();
            return;
        }

        $invoiceValues = General::getInvoiceValues($invoice);

        $this->view->invoiceValues = $invoiceValues;
        $this->view->invoice = $invoice;
        $this->view->records = $invoice->Records;
    }

    public function editrecordAction($id){

        $recordId = $_GET['recordId'];
        $record = InvoiceRecords::findFirst($recordId);

        if($this->request->isPost()){

            $post = $this->request->getPost();
            $saved = $record->saveData($post);

            if($saved){
                $message = [
                    'type' => 'success',
                    'content' => Trans::make('Record has been edited.')
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/invoice/edit/'.$id);
                $this->view->disable();
                return;
            }
            $message = [
                'type' => 'warning',
                'content' => Trans::make('Record cannot be edited. Error corrupted')
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/signadens/invoice/editrecord/'.$id.'?recordId='.$recordId);
            $this->view->disable();
            return;
        }

        $this->view->record = $record;
    }

    public function deleteAction($id){

        $invoice = Invoices::findFirst($id);
        $invoice->softDelete();

        $message = [
            'type' => 'success',
            'content' => Trans::make('Invoice has been deleted.')
        ];
        $this->session->set('message', $message);
        $this->response->redirect('/signadens/invoice/');
        $this->view->disable();
        return;
    }

    public function deleterecordAction($id){

        $recordId = $_GET['recordId'];
        $record = InvoiceRecords::findFirst($recordId);
        $saved = $record->delete();

        if($saved){
            $message = [
                'type' => 'success',
                'content' => Trans::make('Record has been deleted.')
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/signadens/invoice/edit/'.$id);
            $this->view->disable();
            return;
        }
        $message = [
            'type' => 'warning',
            'content' => Trans::make('Record cannot be deleted. Error corrupted')
        ];
        $this->session->set('message', $message);
        $this->response->redirect('/signadens/invoice/editrecord/'.$id.'?recordId='.$recordId);
        $this->view->disable();
        return;
    }

    public function printAction($id){

        $invoice = Invoices::findFirst($id);
        $invoiceValues = General::getInvoiceValues($invoice);

        $this->view->invoiceValues = $invoiceValues;
        $this->view->invoice = $invoice;
        $this->view->records = $invoice->Records;
    }
}
