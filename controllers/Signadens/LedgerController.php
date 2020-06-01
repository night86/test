<?php

namespace Signa\Controllers\Signadens;

use Signa\Models\CodeLedger;
use Signa\Models\Products;

class LedgerController extends InitController
{
    public function indexAction(){

        $this->view->codes = CodeLedger::find('organisation_id = '.$this->currentUser->getOrganisationId());
    }

    public function addAction(){

        if($this->request->isPost()){

            $post = $this->request->getPost();
            $post['added_type'] = 1;
            $code = new CodeLedger();
            $saved = $code->setDatas($post);

            if($saved){
                $message = [
                    'type' => 'success',
                    'content' => 'New ledger code has been added.'
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/ledger/');
                $this->view->disable();
                return;
            }
            $message = [
                'type' => 'warning',
                'content' => 'New ledger code cannot be added. Code must be unique'
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/signadens/ledger/add');
            $this->view->disable();
            return;
        }

//        $this->view->products = self::productsToSelect();
    }

    public function editAction($id){

        if($this->request->isPost()){

            $post = $this->request->getPost();
            $code = CodeLedger::findFirst($id);
            $saved = $code->setDatas($post);

            if($saved){
                $message = [
                    'type' => 'success',
                    'content' => 'Ledger code has been edited.'
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/ledger/');
                $this->view->disable();
                return;
            }
            $message = [
                'type' => 'warning',
                'content' => 'Ledger code cannot be edited.'
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/signadens/ledger/edit/'.$id);
            $this->view->disable();
            return;
        }

        $code = CodeLedger::findFirst($id);
        $this->view->code = $code;
        $this->view->products = self::productsToSelect();
    }

    public function activateAction($id){

        $code = CodeLedger::findFirst($id);
        $status = $code->activateDeactivate(true);

        if($status){
            $message = [
                'type' => 'success',
                'content' => 'Ledger code has been activated.'
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/signadens/ledger/');
            $this->view->disable();
            return;
        }
        $message = [
            'type' => 'warning',
            'content' => 'Ledger code cannot be activated.'
        ];
        $this->session->set('message', $message);
        $this->response->redirect('/signadens/ledger/');
        $this->view->disable();
        return;
    }

    public function deactivateAction($id){

        $code = CodeLedger::findFirst($id);
        $status = $code->activateDeactivate(false);

        if($status){
            $message = [
                'type' => 'success',
                'content' => 'Ledger code has been deactivated.'
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/signadens/ledger/');
            $this->view->disable();
            return;
        }
        $message = [
            'type' => 'warning',
            'content' => 'Ledger code cannot be deactivated.'
        ];
        $this->session->set('message', $message);
        $this->response->redirect('/signadens/ledger/');
        $this->view->disable();
        return;
    }

    private static function productsToSelect(){

        $products = Products::find('approved = 1 AND deleted = 0');
        $productsArr = array();

        foreach ($products as $product){

            $productsArr[$product->getId()] = $product->getName().' (from: '.$product->Organisation->getName().', price: &euro;'.$product->price.')';
        }
        return $productsArr;
    }
}
