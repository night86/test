<?php

namespace Signa\Controllers\Signadens;

use Mpdf\Tag\Code;
use Signa\Models\CodeTariff;
use Signa\Models\Recipes;
use Signa\Models\Products;
use Signa\Models\CodeLedger;
use Signa\Models\ProductCategories;
use Signa\Helpers\Translations as Trans;

class TariffController extends InitController
{
    public function indexAction(){

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
        $this->view->codes = CodeTariff::find('organisation_id = '.$this->currentUser->getOrganisationId());
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
                    'content' => Trans::make('New tariff code has been added.')
                ];

                $this->session->set('message', $message);
                $this->response->redirect('/signadens/tariff/');
                $this->view->disable();
                return;
            }
            else {
                $message = [
                    'type' => 'warning',
                    'content' => Trans::make('This tariff code already exists. Tariff codes should be unique.')
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/tariff/add');
                $this->view->disable();
                return;
            }
        }

        $this->view->recipes = $this->recipesToSelect();
    }

    public function editAction($id){

        // Find tariff code
        $code = CodeTariff::findFirst($id);

        // Find all ledger codes
        $ledgerCodes = CodeLedger::find('organisation_id = '.$this->currentUser->getOrganisationId().' AND active = 1');

        $recipes = $this->recipesToSelect($id);

        if($this->request->isPost()){

            $post = $this->request->getPost();

            $code->setDescription($post['description']);
            $code->setPrice($post['price']);
            $code->setOptions(json_encode($post['options']));
            $code->setLedgerSalesId(!empty($post['ledger_sales_id']) ? $post['ledger_sales_id'] : NULL);
            $code->save();

            $message = [
                'type' => 'success',
                'content' => 'Tariff code has been edited.'
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/signadens/tariff/');
            $this->view->disable();
            return;
        }

        // View vars
        $this->view->ledgerCodes = $ledgerCodes;
        $this->view->code = $code;
        $this->view->options = $code->getOptions();
        $this->view->recipes = $recipes;
    }

    public function activateAction($id){

        $code = CodeTariff::findFirst($id);
        $status = $code->activateDeactivate(true);

        if($status){
            $message = [
                'type' => 'success',
                'content' => 'Tariff code has been activated.'
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/signadens/tariff/');
            $this->view->disable();
            return;
        }
        $message = [
            'type' => 'warning',
            'content' => 'Tariff code cannot be activated.'
        ];
        $this->session->set('message', $message);
        $this->response->redirect('/signadens/tariff/');
        $this->view->disable();
        return;
    }

    public function deactivateAction($id){

        $code = CodeTariff::findFirst($id);
        $status = $code->activateDeactivate(false);

        if($status){
            $message = [
                'type' => 'success',
                'content' => 'Tariff code has been deactivated.'
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/signadens/tariff/');
            $this->view->disable();
            return;
        }
        $message = [
            'type' => 'warning',
            'content' => 'Tariff code cannot be deactivated.'
        ];
        $this->session->set('message', $message);
        $this->response->redirect('/signadens/tariff/');
        $this->view->disable();
        return;
    }

    public function ajaxmarginsettingsAction(){

        $this->view->disable();

        if ($this->request->isPost()) {

            $post = $this->request->getPost();

            if($post['id'] !== null && $post['margin_type'] !== null && $post['margin_value'] !== null && $post['rounding_type'] !== null) {

                if($post['req_type'] == 'all'){

                    $products = Products::find("approved = 1 AND active = 1 AND tariff_id IS NOT NULL AND (main_category_id = ".$post['id']." OR sub_category_id = ".$post['id']." OR sub_sub_category_id = ".$post['id'].")");

                    foreach ($products as $p){

                        $p->Tariff->setMarginType($post['margin_type']);
                        $p->Tariff->setMarginValue($post['margin_value']);
                        $p->Tariff->setRoundingType($post['rounding_type']);
                        $p->Tariff->save();
                    }

                    $result = [
                        "status"    => "ok",
                        "msg"       => Trans::make("Tariff assigned for this product category")
                    ];
                }
                else {
                    $tariffCode = CodeTariff::findFirst($post['id']);
                    $tariffCode->setMarginType($post['margin_type']);
                    $tariffCode->setMarginValue($post['margin_value']);
                    $tariffCode->setRoundingType($post['rounding_type']);
                    $tariffCode->save();

                    $result = [
                        "status"    => "ok",
                        "msg"       => Trans::make("Margin settings added successfully")
                    ];
                }
            }
            else {
                $result = [
                    "status"    => "error",
                    "msg"       => Trans::make("Error while adding margin settings")
                ];
            }
            return json_encode($result);
        }
    }

    private function recipesToSelect($codeId=null){

        $codes = CodeTariff::find($codeId)->toArray();

        $recipesArr = [];
        $recipes=[];

        foreach ($codes as $key => $value) {

            if(is_null($codeId) || is_null( $value['recipe_id'])) {

                $recipes[] = Recipes::findFirst('active = 1');
            }
            else {
                $recipes[] = Recipes::findFirst('active = 1 AND id = ' . $value['recipe_id']);
            }
        }

        foreach ($recipes as $recipe){

            if(!$recipe){
                continue;
            }
            $product = Products::getCurrentProduct($recipe->getProductId());
            $productName = is_null($product) ? '' : ' (product: '.$product->getName().')';
            $recipesArr[$recipe->getId()] = $recipe->getName().$productName;
        }
        return $recipesArr;
    }
}
