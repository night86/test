<?php

namespace Signa\Controllers\Lab;

use Signa\Models\MapLabTariffLedger;
use Signa\Models\CodeTariff;
use Signa\Models\CodeLedger;
use Signa\Models\OrderShortlist;
use Signa\Models\Organisations;
use Signa\Models\Recipes;
use Signa\Models\ProductCategories;
use Signa\Models\Products;
use Signa\Helpers\User;
use Signa\Helpers\Translations as T;

class SalesTariffController extends InitController
{
    public function indexAction(){

        $codes = CodeTariff::find('organisation_id = '.$this->currentUser->getOrganisationId());
        $this->view->codes = $codes;
    }

    public function addAction(){

        if($this->request->isPost()){

            $post = $this->request->getPost();
            $post['added_type'] = 1;

            $checkCode = CodeTariff::findFirst('code = '.$post['code']);

            if($checkCode == false){

                $code = new CodeTariff();
                $code->setCode($post['code']);
                $code->setDescription($post['description']);
                $code->setPrice($post['price']);
                $code->save();

                $message = [
                    'type' => 'success',
                    'content' => T::make('New tariff code has been added.')
                ];

                $this->session->set('message', $message);
                $this->response->redirect('/lab/sales_tariff/');
                $this->view->disable();
                return;
            }
            else {
                $message = [
                    'type' => 'warning',
                    'content' => T::make('This tariff code already exists. Tariff codes should be unique.')
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/lab/sales_tariff/add');
                $this->view->disable();
                return;
            }
        }

        $this->view->recipes = $this->recipesToSelect();
    }

    public function editAction($id){

        $code = CodeTariff::findFirst($id);

        if($this->request->isPost()){

            $post = $this->request->getPost();

            $code->setDescription($post['description']);
            $code->setPrice($post['price']);
            $code->setOptions(json_encode($post['options']));
            $code->setLedgerSalesId($post['ledger_sales_id']);
            $code->save();

            $message = [
                'type' => 'success',
                'content' => 'Tariff code has been edited.'
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/lab/sales_tariff/');
            $this->view->disable();
            return;
        }

        $this->view->code = $code;
        $this->view->recipes = $this->recipesToSelect();
    }

    public function activateAction($id){

        $code = CodeTariff::findFirst($id);
        $status = $code->activateDeactivate(true);

        if($status){
            $message = [
                'type' => 'success',
                'content' => 'Tariff code has been activated.',
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/lab/sales_tariff/');
            $this->view->disable();
            return;
        }
        $message = [
            'type' => 'warning',
            'content' => 'Tariff code cannot be activated.',
        ];
        $this->session->set('message', $message);
        $this->response->redirect('/lab/sales_tariff/');
        $this->view->disable();
        return;
    }

    public function deactivateAction($id){

        $code = CodeTariff::findFirst($id);
        $status = $code->activateDeactivate(false);

        if($status){
            $message = [
                'type' => 'success',
                'content' => 'Tariff code has been deactivated.',
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/lab/sales_tariff/');
            $this->view->disable();
            return;
        }
        $message = [
            'type' => 'warning',
            'content' => 'Tariff code cannot be deactivated.',
        ];
        $this->session->set('message', $message);
        $this->response->redirect('/lab/sales_tariff/');
        $this->view->disable();
        return;
    }

    private function recipesToSelect(){

        $recipes = Recipes::find('deleted_at IS NULL AND lab_id = '.$this->currentUser->getOrganisationId().' AND active = 1');
        $recipesArr = array();

        /** @var Recipes $recipe */
        foreach ($recipes as $recipe){

            $recipesArr[$recipe->getId()] = $recipe->getName();
            $product = Products::getCurrentProduct($recipe->getProductId());

            if ($product) {
                $recipesArr[$recipe->getId()] .= ' (product: ' . $product->getName() . ')';
            }
        }
        return $recipesArr;
    }

    public function mapAction(){

        if($this->request->isPost()){

            $post = $this->request->getPost();
            $postArr = self::postTariffIdsToArr($post);

            foreach($postArr as $key => $postField){

                $map = MapLabTariffLedger::findFirst('tariff_id = '.$key);

                // If map exist and value = 0 then remove map
                if($map && $postField['tariff'] == 0 && $postField['ledger'] == 0 && $postField['product'] == 0){

                    $map->delete();
                    $status = true;
                }

                // If map doesn't exist then create new map object
                if($map == false){
                    $map = new MapLabTariffLedger();
                }

                // If tariff code is selected then assign values to new map object or update old object
                if($postField['ledger'] > 0 || $postField['tariff'] > 0 || $postField['product'] > 0){

                    $map->setTariffId($key);
                    $map->setSignaTariffId($postField['tariff']);
                    $map->setLedgerId($postField['ledger']);
                    $map->setProductId($postField['product']);
                    $map->save();
                }
            }
            $message = [
                'type' => 'success',
                'content' => 'Saved.',
            ];
            $this->session->set('message', $message);
        }

        $this->assets->collection('footer')
            ->addJs("js/app/mapSigna.js");

        $maps = MapLabTariffLedger::find();
        $mapsArr = array();

        foreach ($maps as $map){

            $mapsArr[$map->getTariffId()] = array(
                'tariff' => (int)$map->getSignaTariffId(),
                'product' => (int)$map->getProductId(),
                'ledger' => (int)$map->getLedgerId(),
            );
        }

        $organisationId = $this->currentUser->getOrganisationId();
        $signaOrganisationIdsArr = User::getSignaOrganisationIds();
        $signaOrganisationIds = implode(',', $signaOrganisationIdsArr);

        $this->view->maps = $mapsArr;
        $this->view->shortlists = OrderShortlist::find('organisation_id = '.$organisationId);
        $this->view->ledgers = CodeLedger::find('organisation_id = '.$organisationId);
        $this->view->tariffs = CodeTariff::find('organisation_id = '.$organisationId);
        $this->view->signaTariffs = CodeTariff::find('organisation_id IN ('.$signaOrganisationIds.')');
    }

    public function mappingandmarginsAction(){

        $signaOrg = Organisations::findFirst("email = 'info@ '");

        $categories = [];
        $final = [];
        $productCategories = ProductCategories::find(array('deleted = 0 AND deleted_at IS NULL AND deleted_by IS NULL'));

        // Create product category temporary tree
        foreach($productCategories as $pc){

            if($pc->getParentId() == NULL){
                $categories[$pc->getId()] = $pc->toArray();
            }
            else {
                if($pc->Parent->Parent){
                    $categories[$pc->Parent->getParentId()]['sub'][$pc->getParentId()]['subsub'][$pc->getId()] = $pc->toArray();
                    $categories[$pc->Parent->getParentId()]['sub'][$pc->getParentId()]['subsub'][$pc->getId()]['sub_parent_name'] = $pc->Parent->getName();
                    $categories[$pc->Parent->getParentId()]['sub'][$pc->getParentId()]['subsub'][$pc->getId()]['cat_parent_name'] = $pc->Parent->Parent->getName();
                }
                else {
                    $categories[$pc->getParentId()]['sub'][$pc->getId()] = $pc->toArray();
                    $categories[$pc->getParentId()]['sub'][$pc->getId()]['cat_parent_name'] = $pc->Parent->getName();
                    $categories[$pc->getParentId()]['sub'][$pc->getId()]['sub_parent_name'] = NULL;
                }
            }
        }

        // Create list from lowest level available
        foreach($categories as $cat){

            if($cat['sub'] != NULL && count($cat['sub']) > 0){

                foreach($cat['sub'] as $sub){

                    if($sub['subsub'] && count($sub['subsub']) > 0){

                        foreach($sub['subsub'] as $subsub){

                            if($subsub != NULL){

                                $final[] = $subsub;
                            }
                        }
                    }
                    else {
                        $final[] = $sub;
                    }
                }
            }
            else {
                $final[] = $cat;
            }
        }

        // View vars and assets
        $this->view->productCategories = $final;
        $this->view->signaId = $signaOrg->getId();
        $this->view->signaTariffs = CodeTariff::find('active = 1 AND organisation_id IN('.$signaOrg->getId().', '.$this->currentUser->getOrganisationId().')');
    }

    public function ajaxmaptariffAction(){

        $this->view->disable();

        if ($this->request->isPost()) {

            $post = $this->request->getPost();

            if($post['lab_tariff_id'] !== null && $post['signa_tariff_id'] !== null) {

                $tariffCode = CodeTariff::findFirst($post['lab_tariff_id']);
                $tariffCode->setSignaTariffId($post['signa_tariff_id']);
                $tariffCode->save();

                $result = [
                    "status"    => "ok",
                    "msg"       => T::make("Tariff code mapped successfully")
                ];
            }
            else {
                $result = [
                    "status"    => "error",
                    "msg"       => T::make("Error while mapping tariff code")
                ];
            }
            return json_encode($result);
        }
    }

    public function ajaxremovetariffAction(){

        $this->view->disable();

        if ($this->request->isPost()) {

            $post = $this->request->getPost();

            if($post['id'] !== null) {

                $tariffCode = CodeTariff::findFirst('signa_tariff_id = '.$post['id']);
                $tariffCode->setSignaTariffId(NULL);
                $tariffCode->save();

                $result = [
                    "status"    => "ok",
                    "msg"       => T::make("Tariff code mapping was removed")
                ];
            }
            else {
                $result = [
                    "status"    => "error",
                    "msg"       => T::make("Error while removing tariff code")
                ];
            }
            return json_encode($result);
        }
    }

    public function ajaxremovemarginAction(){

        $this->view->disable();

        if ($this->request->isPost()) {

            $post = $this->request->getPost();

            if($post['id'] !== null) {

                $tariffCode = CodeTariff::findFirst($post['id']);
                $tariffCode->setMarginTypeLab(NULL);
                $tariffCode->setMarginValueLab(NULL);
                $tariffCode->setRoundingTypeLab(NULL);
                $tariffCode->save();

                $result = [
                    "status"    => "ok",
                    "msg"       => T::make("Margin settings removed")
                ];
            }
            else {
                $result = [
                    "status"    => "error",
                    "msg"       => T::make("Error while removing margin settings")
                ];
            }
            return json_encode($result);
        }
    }

    public function ajaxmarginsettingsAction(){

        $this->view->disable();

        if ($this->request->isPost()) {

            $post = $this->request->getPost();

            if($post['id'] !== null && $post['margin_type'] !== null && $post['margin_value'] !== null && $post['rounding_type'] !== null) {

                if($post['req_type'] == 'all'){

                    $products = Products::find("approved = 1 AND active = 1 AND tariff_id IS NOT NULL AND (main_category_id = ".$post['id']." OR sub_category_id = ".$post['id']." OR sub_sub_category_id = ".$post['id'].")");

                    foreach ($products as $p){

                        $p->Tariff->setMarginTypeLab($post['margin_type']);
                        $p->Tariff->setMarginValueLab($post['margin_value']);
                        $p->Tariff->setRoundingTypeLab($post['rounding_type']);
                        $p->Tariff->save();
                    }

                    $result = [
                        "status"    => "ok",
                        "msg"       => T::make("Tariff assigned for this product category")
                    ];
                }
                else {
                    $tariffCode = CodeTariff::findFirst($post['id']);
                    $tariffCode->setMarginTypeLab($post['margin_type']);
                    $tariffCode->setMarginValueLab($post['margin_value']);
                    $tariffCode->setRoundingTypeLab($post['rounding_type']);
                    $tariffCode->save();

                    $result = [
                        "status"    => "ok",
                        "msg"       => T::make("Margin settings added successfully")
                    ];
                }
            }
            else {
                $result = [
                    "status"    => "error",
                    "msg"       => T::make("Error while adding margin settings")
                ];
            }
            return json_encode($result);
        }
    }

    private static function postTariffIdsToArr($post){

        $tariffId = 0;
        $mapFields = array('tariff', 'ledger', 'product');
        $dataArr = array();

        foreach($post as $key => $postValue){

            preg_match('/\d+/', $key, $matches);

            if(count($matches) > 0){
                $tariffId = $matches[0];
            }

            foreach($mapFields as $mapField){

                $checkString = $mapField.'-'.$tariffId;

                if($checkString === $key){

                    $dataArr[$tariffId][$mapField] = (int)$postValue;
                }
            }
        }
        return $dataArr;
    }
}
