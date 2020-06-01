<?php

namespace Signa\Controllers\Lab;

use Signa\Libs\Products\ProductsData;
use Signa\Libs\Products\ProductsFilters;
use Signa\Libs\Products\ProductsList;
use Signa\Libs\Solr;
use Signa\Models\CodeLedger;
use Signa\Models\Departments;
use Signa\Models\OrderCartProduct;
use Signa\Models\OrderCart;
use Signa\Models\Organisations;
use Signa\Models\Products;
use Signa\Models\OrderShortlist;
use Signa\Models\CodeTariff;
use Signa\Helpers\User;
use Signa\Helpers\Translations as Trans;
use Signa\Models\MapLabTariffLedger;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;
use Signa\Models\Purchase;
use Signa\Models\UserDepartments;
use Signa\Models\Users;
use Signa\Helpers\Date;


class ProductController extends InitController
{
    public function indexAction(){

        if (!$this->session->has('products-filters')) {

            $this->session->set('products-filters', [
                'query' => '',
                'filter' => [],
                'page' => 1,
                'limit' => 6,
            ]);
        }

        $productsList = new ProductsList();

        /** @var ProductsFilters $filters */
        $filters = $productsList->getFilters();

        $this->assets->collection('footerNotCompile')
            ->addJs("js/app/products.js")
            ->addJs("js/app/productsList.js?v=2")
            ->addJs("bower_components/bootstrap3-typeahead/bootstrap3-typeahead.min.js");

        $this->view->filters = $filters;
        $this->view->addShortlistContent = $this->getShortlistContent();
    }

    public function getFilteredProductsAction($useSession = false, $supplierId = null, $limitPerPage = null){

        if (!$this->request->isAjax()) {
            return false;
        }

        $pd = new ProductsData();
        $pd->setUseSession($useSession);

        if ($supplierId && $supplierId != 0) {

            $pd->setSupplierId($supplierId);
        }

        if ($limitPerPage && $limitPerPage != 0) {

            $pd->setLimitPerPage($limitPerPage);
        }
        $data = $pd->getFilteredProducts();

        echo json_encode($data); die;
    }

    public function addcartAction($id){

        $this->view->disable();

        $amount = $this->request->getPost('amount');
        $amount = (is_null($amount)) ? 1 : $amount;
        $shortlistId = $this->request->getPost('shortlistId');
        $orderListName = $this->request->getPost('order_list');

        $orderCart = OrderCart::findFirst('status = 1 AND organisation_id = '.$this->currentUser->Organisation->getId());

        if(!is_null($orderListName)){

            if($orderListName == ''){

                $orderListName = null;
                $productExist = OrderCartProduct::findFirst('order_cart_id = '.$orderCart->getId().' AND product_id = '.$id.' AND project_no IS NULL');
            }
            else {
                $productExist = OrderCartProduct::findFirst('order_cart_id = '.$orderCart->getId().' AND product_id = '.$id.' AND project_no LIKE \''.$orderListName.'\'');
            }

            if((bool)$productExist === false){

                $product = Products::findFirst($id);
                $saveData = array('amount' => $amount, 'product_id' => $id, 'order_cart_id' => $orderCart->getId(), 'project_no' => $orderListName, 'supplier_id' => $product->getSupplierId());
                $orderCartProduct = new OrderCartProduct();
                $orderCartProduct->saveData($saveData);

                if(!is_null($shortlistId)){

                    $shortlistObject = OrderShortlist::findFirst((int)$shortlistId);
                    $orderCartProduct->setPrice($shortlistObject->getProductPrice(false));
                }
                else {
                    $orderCartProduct->setPrice($product->getPrice(false));
                }
                $saveprod = $orderCartProduct->save();
                return json_encode(array('status' => true, 'added' => true, 'msg'=>$amount));
            }
            else {
                $newAmount = $productExist->getAmount() + $amount;
                $productExist->setAmount($newAmount);
                $productExist->save();
            }
            return json_encode(array('status' => true, 'added' => false));
        }

        if($orderCart == false){

            $orderCart = new OrderCart();
            $orderCart->createNew();
        }
        $productsInCart = OrderCartProduct::find('order_cart_id = ' . $orderCart->getId() . ' AND product_id =' . $id);

        if (count($productsInCart)) {

            $adder = Users::findFirst('id = ' . $productsInCart[0]->getCreatedBy());
            $userDepartment = UserDepartments::findFirst('user_id  = ' . $adder->getId());
            $department = is_null($userDepartment) ? '' : Departments::findFirst(['id' => $userDepartment]);
            $departmentName = is_null($department) ? '' : $department->getName();

            return json_encode(
                [
                    'status' => false,
                    'product_id' => $id,
                    'exist' => true,
                    'adderName' => $adder->getFirstname() . ' ' . $adder->getLastname(),
                    'department' => $departmentName,
                ]
            );
        }
        return json_encode(array('status' => false, 'product_id' => $id, 'exist' => false));
    }

    public function addshortlistAction($id){

        if($this->request->isAjax()){

            $post = $this->request->getPost();
            $this->view->disable();

            $shortlistExist = OrderShortlist::findFirst('organisation_id = '.$this->currentUser->Organisation->getId().' AND product_id = '.$id);

            if((bool)$shortlistExist === false){

                $shortlist = new OrderShortlist();

                if(!empty($post['margin_type']) && !empty($post['margin_value'])){

                    $shortlist->setMarginType($post['margin_type']);
                    $shortlist->setMarginValue($post['margin_value']);
                    $shortlist->setRoundDirection($post['round_direction']);
                    $shortlist->setRoundType($post['round_type']);
                }
                $shortlist->createNew($id, 1, NULL, NULL);

                return json_encode(array('added' => true));
            }
            return json_encode(array('added' => false));
        }
    }

    public function showAction($id, $mid){

        $product = Products::findFirst($id);
        $purchase = Purchase::findFirst([
            'conditions' => [
                '_id' => new \MongoDB\BSON\ObjectId($mid),
            ],
        ]);

        $purchase->isopened = true;
        $purchase->save();

        return $this->response->redirect($this->request->getHTTPReferer());
    }

    public function addshortlistbulkAction(){

        if($this->request->isAjax()) {

            $post = $this->request->getPost();

            $this->view->disable();

            if(!empty($post['id'])){

                $failure = array();

                foreach($post['id'] as $p){

                    $shortlistExist = OrderShortlist::findFirst('organisation_id = '.$this->currentUser->Organisation->getId().' AND product_id = '.$p);

                    if((bool)$shortlistExist === false) {

                        $shortlist = new OrderShortlist();
                        $shortlist->createNew($p, 1, NULL, NULL);
                    }
                    else {
                        array_push($failure, $p);
                    }
                }

                if(!empty($failure)){

                    $result = [
                        'status'    => 'error',
                        'msg'       => (count($failure) > 1) ? implode(", ", $failure).Trans::make(' are already in the shortlist', "en_US") : Trans::make('Product already in the shortlist', "en_US"),
                        'details'   => $failure,
                    ];
                }
                else {
                    $result = [
                        'status'    => 'ok',
                        'msg'       => Trans::make('Products added to the shortlist', "en_US"),
                        'details'   => NULL,
                    ];
                }

                return json_encode($result);
            }
            else {
                $result = [
                    'status'    => 'error',
                    'msg'       => Trans::make('Please select a product', "en_US"),
                    'details'   => NULL,
                ];
                return json_encode($result);
            }
        }
    }

    public function addshortlistsupplierAction(){

        if($this->request->isAjax()) {

            $this->view->disable();
            $post = $this->request->getPost();
            $currentDate = Date::currentDate();
            $organisationId = $this->currentUser->Organisation->getId();

            $supplierProducts = Products::query()
                ->where('supplier_id = :supplier_id: AND deleted = 0 AND approved = 1 AND active = 1 AND declined = 0')
                ->andWhere('skipped IS NULL')
                ->andWhere('start_date <= :current_date:')
                ->columns([
                    'id'
                ])
                ->bind([
                    'supplier_id' => $post['id'],
                    'current_date' => $currentDate,
                ])
                ->execute();

            foreach($supplierProducts as $sp){

                $shortlistExist = OrderShortlist::count('organisation_id = '.$organisationId.' AND product_id = '.$sp->id);

                if($shortlistExist === 0) {

                    $shortlist = new OrderShortlist();
                    $shortlist->createNew($sp->id, 1, NULL, NULL);
                }
            }

            $result = [
                'status'    => 'ok',
                'msg'       => Trans::make('Products added to shortlist', "en_US"),
                'details'   => NULL,
            ];

            return json_encode($result);
        }
    }

    public function ajaxnamessimpleAction(){

        if($this->request->isAjax()){

            $this->view->disable();

            $pd = new ProductsData();
            $pd->setAutosuggest(true);
            $data = $pd->getFilteredProducts();

            echo json_encode($data); die;
        }
    }

    public function ajaxnamesAction(){

        if($this->request->isAjax()){

            $this->view->disable();

            $products = Products::find(array(
                'approved = 1 AND declined = 0 AND deleted = 0 AND active = 1',
                'columns' => array('id', 'code', 'name'),
            ));
            $productsNameArr = array();

            foreach ($products as $product){

                if(!in_array($product->name, $productsNameArr)){

                    $productsNameArr[] = $product->id." - ".$product->code." _".$product->name;
                }
            }
            return json_encode($productsNameArr);
        }
    }

    private function getShortlistContent(){

        $tariffCodes = CodeTariff::find('organisation_id = '.$this->currentUser->getOrganisationId());
        $ledgerCodes = CodeLedger::find('organisation_id = '.$this->currentUser->getOrganisationId());
        $marginTypes = array(
            1 => Trans::make('Fixed price'),
            2 => Trans::make('Fixed margin in euro'),
            3 => Trans::make('As percentages of the purchase price'),
            4 => Trans::make('As percentages of the sales price'),
        );
        $roundDirection = array(
            1 => Trans::make('Up'),
            2 => Trans::make('Down'),
        );
        $roundType = array(
            1 => Trans::make('Decimal'),
            2 => Trans::make('Integer'),
        );

        $signaOrganisationIdsArr = User::getSignaOrganisationIds();
        $signaOrganisationIds = implode(',', $signaOrganisationIdsArr);
        $signaTariffCodes = CodeTariff::find('organisation_id IN ('.$signaOrganisationIds.')');

        $html = '<p>'.Trans::make("To add the project you need to set the minimum order amount quantity and optional set the tariff code for sales").'</p>';
        $html .= '<label>'.Trans::make("Min. order quantity").'</label>';
        $html .= '<input type="number" class="form-control whole-form" id="input-amount" name="min-amount" value="" min="" /><br />';
        $html .= '<label class="radio-inline"><input type="radio" name="product-type" class="whole-form" value="1" checked="checked">'.Trans::make("Sales").'</label>';
        $html .= '<label class="radio-inline"><input type="radio" name="product-type" class="whole-form" value="2">'.Trans::make("Usage").'</label><br /><br />';
        $html .= '<div class="tariff-code-container"><label>'.Trans::make("Tariff code").'</label> <a href="#" class="btn-sm btn-success pull-right" id="add-tariff-code"><i class="pe-7s-plus"></i></a>';
        $html .= '<select name="tariff" class="form-control whole-form tariff-code">';

        foreach ($tariffCodes as $tariffCode){

            $html .= '<option value="'.$tariffCode->getId().'">'.$tariffCode->getCode().' - '.$tariffCode->getDescription().'</option>';
        }
        $html .= '</select></div>';
        $html .= '<div class="ledger-code-container" style="display: none;"><label>'.Trans::make("Ledger code").'</label>';
        $html .= '<select name="ledger" class="form-control whole-form ledger-code">';

        foreach ($ledgerCodes as $ledgerCode){

            $html .= '<option value="'.$ledgerCode->getId().'">'.$ledgerCode->getCode().' - '.$ledgerCode->getDescription().'</option>';
        }
        $html .= '</select></div>';
        $html .= '<div class="tarrif-code-form" style="display: none">';
        $html .= '<input type="number" min="0" class="form-control whole-form tarrif-form" id="input-tarrif-code" name="code" />';
        $html .= '<label>'.Trans::make("Description").'</label><input type="text" class="form-control whole-form tarrif-form" id="input-tarrif-code-description" name="description" />';
        $html .= '<label>'.Trans::make("Margin type").'</label><select name="margin_type" class="form-control whole-form" id="margin-type">';

        foreach ($marginTypes as $key => $marginType){

            $html .= '<option value="'.$key.'">'.$marginType.'</option>';
        }
        $html .= '</select>';
        $html .= '<label>'.Trans::make("Margin value/percentage").'</label><input type="text" class="form-control whole-form tarrif-form" id="margin-value" name="margin_value" />';
        $html .= '<label>'.Trans::make("Round direction").'</label><select name="round_direction" class="form-control whole-form" id="round-direction">';

        foreach ($roundDirection as $key => $value){

            $html .= '<option value="'.$key.'">'.$value.'</option>';
        }
        $html .= '</select>';
        $html .= '<label>'.Trans::make("Round type").'</label><select name="round_type" class="form-control whole-form" id="round-type">';

        foreach ($roundType as $key => $value){

            $html .= '<option value="'.$key.'">'.$value.'</option>';
        }
        $html .= '</select>';
        $html .= '<label>'.Trans::make("Price").'</label><input type="number" class="form-control whole-form tarrif-form" id="input-tarrif-code-price" min="0" step="any" name="price" />';
        $html .= '<label>'.Trans::make("Signadens code").'</label><select name="signa-tariff-code" class="form-control whole-form" id="input-signa-tariff-code">';

        foreach ($signaTariffCodes as $signaTariffCode){

            $html .= '<option value="'.$signaTariffCode->getId().'">'.$signaTariffCode->getCode().' - '.$signaTariffCode->getDescription().'</option>';
        }
        $html .= '</select></div>';

        return $html;
    }

    public function ajaxcontactproductAction(){

        $this->view->disable();

        if ($this->request->isPost()) {

            if($this->request->getPost('content') !== null) {

                $params = array(
                    'email' => $this->currentUser->getEmail(),
                    'username' => $this->currentUser->getFirstname().' '.$this->currentUser->getLastname(),
                    'userlab' => $this->currentUser->Organisation->getName(),
                    'content' => $this->request->getPost('content')
                );

                $sended = $this->mail->send('info@ ', Trans::make('New request on a product that can’t be found'), 'productInfo', $params);

                if($sended){

                    $result = [
                        "status"    => "ok",
                        "msg"       => Trans::make("Message sent")
                    ];
                }
                else {
                    $result = [
                        "status"    => "error",
                        "msg"       => Trans::make("Error while sending message")
                    ];
                }
            }
            else {
                $result = [
                    "status"    => "error",
                    "msg"       => Trans::make("Message cannot be empty")
                ];
            }
            return json_encode($result);
        }
    }
}
