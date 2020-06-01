<?php

namespace Signa\Controllers\Lab;

use Signa\Models\CodeLedger;
use Signa\Models\CodeTariff;
use Signa\Models\Products;
use Signa\Models\MapSignaLedgerTariff;
use Signa\Helpers\User as UserHelper;

class SalesLedgerController extends InitController
{
    public function indexAction(){

        $codes = CodeLedger::find('organisation_id = '.$this->currentUser->getOrganisationId());
        $this->view->codes = $codes;
    }

    public function addAction(){

        if($this->request->isPost()){

            $post = $this->request->getPost();
            $post['added_type'] = 1;
            $code = CodeLedger::findFirst('code=' . $post['code'] . ' AND organisation_id=' . $this->currentUser->getOrganisationId());

            if($code!=false) {

                $message = [
                    'type' => 'warning',
                    'content' => 'New ledger code cannot be added. Code must be unique'
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/lab/sales_ledger/add');
                $this->view->disable();
                return;
            }
            $code = new CodeLedger();
            $saved = $code->setDatas($post);

            if($saved){
                $code->activateDeactivate(true);
                $message = [
                    'type' => 'success',
                    'content' => 'New ledger code has been added.'
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/lab/sales_ledger/');
                $this->view->disable();
                return;
            }
            $message = [
                'type' => 'warning',
                'content' => 'New ledger code cannot be added. Code must be unique'
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/lab/sales_ledger/add');
            $this->view->disable();
            return;
        }

        $this->view->products = self::productsToSelect();
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
                $this->response->redirect('/lab/sales_ledger/');
                $this->view->disable();
                return;
            }
            $message = [
                'type' => 'warning',
                'content' => 'Ledger code cannot be edited.'
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/lab/sales_ledger/edit/'.$id);
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
            $this->response->redirect('/lab/sales_ledger/');
            $this->view->disable();
            return;
        }
        $message = [
            'type' => 'warning',
            'content' => 'Ledger code cannot be activated.'
        ];
        $this->session->set('message', $message);
        $this->response->redirect('/lab/sales_ledger/');
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
            $this->response->redirect('/lab/sales_ledger/');
            $this->view->disable();
            return;
        }
        $message = [
            'type' => 'warning',
            'content' => 'Ledger code cannot be deactivated.'
        ];
        $this->session->set('message', $message);
        $this->response->redirect('/lab/sales_ledger/');
        $this->view->disable();
        return;
    }

    public function mapAction()
    {
        if($this->request->isPost())
        {
            $post = $this->request->getPost();
            $status = false;

            foreach($post as $key => $tarrif_id)
            {
                $ledger_id = (int)str_replace('ledger-', '', $key);
                $map = MapSignaLedgerTariff::findFirst('ledger_id = '.$ledger_id);

                // If map exist and value = 0 then remove map
                if($map && $tarrif_id == 0)
                {
                    $map->delete();
                    $status = true;
                }
                // If map doesn't exist then create new map object
                if($map == false){
                    $map = new MapSignaLedgerTariff();
                }
                // If tariff code is selected then assigna values to new map object or update old object
                if($tarrif_id > 0 && $map->getTariffId() !== $tarrif_id)
                {
                    $map->setLedgerId($ledger_id);
                    $map->setTariffId($tarrif_id);
                    $map->save();
                    $status = true;
                }

            }
            return json_encode(array('status' => $status));
        }

        $this->assets->collection('footer')
            ->addJs("js/app/mapSigna.js");

        $maps = MapSignaLedgerTariff::find();
        $mapsArr = array();
        foreach ($maps as $map)
        {
            $mapsArr[$map->getLedgerId()] = (int)$map->getTariffId();
        }

        $organisationId = $this->currentUser->Organisation->getId();
        $signaOrganisationIdsArr = UserHelper::getSignaOrganisationIds();
        $signaOrganisationIds = implode(',', $signaOrganisationIdsArr);

        $this->view->maps = $mapsArr;
        $this->view->ledgers = CodeLedger::find('organisation_id = '.$organisationId);
        $this->view->tariffs = CodeTariff::find('organisation_id IN ('.$signaOrganisationIds.')');
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