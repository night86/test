<?php

namespace Signa\Controllers\Lab;

use Signa\Models\LogLabOrderStatus;
use Signa\Models\OrderCart;
use Signa\Models\OrderCartProduct;
use Signa\Models\Products;
use Signa\Helpers\Date;
use Signa\Helpers\Translations as Trans;

class OrderController extends InitController
{
    public function indexAction(){

    }

    public function historyAction(){

    }

    public function ajaxorderlistAction($history = null){

        $this->view->disable();

        if ($history) {
            $carts = OrderCart::find("deleted_at IS NULL AND status = 4 AND organisation_id = " . $this->currentUser->getOrganisationId());
        }
        else {
            $carts = OrderCart::find("deleted_at IS NULL AND status != 4 AND organisation_id = " . $this->currentUser->getOrganisationId());
        }
        $dataArr = array();

        if(isset($carts) && count($carts)){

            foreach ($carts as $key => $cart){

                $orderedBy = is_null($cart->getOrderBy()) ? '-' : $cart->OrderBy->getFullName();
                $department = '-';

                if(!is_null($cart->getOrderBy())) {

                    $department = is_null($cart->OrderBy->getDepartmentName()) ? '-' : $cart->OrderBy->getDepartmentName();
                }
                $orderedAt = is_null($cart->getOrderAt()) ? '-' : Date::formatToDefault($cart->getOrderAt());
                $cartProducts = $cart->OrderCartProduct;
                $cartProductArr = array();
                $total = 0;

                /** @var OrderCartProduct $cartProduct */
                foreach ($cartProducts as $cartProduct){

                    $product = Products::getCurrentProduct($cartProduct->getProductId());

                    if($product && $product->getPrice() != NULL){
                        $total += $cartProduct->getAmount() * $product->getPrice();
                    }

                    if(!in_array($cartProduct->Organisation->getName(), $cartProductArr)){
                        $cartProductArr[] = $cartProduct->Organisation->getName();
                    }
                }
                $suppliers = count($cartProductArr) > 0 ? implode(' ', $cartProductArr) : '-';

                $dataArr[$key]['name'] = $cart->getName();
                $dataArr[$key]['total'] = number_format($total, 2, '.', '');
                $dataArr[$key]['suppliers'] = $suppliers;
                $dataArr[$key]['order_by'] = $orderedBy;
                $dataArr[$key]['department'] = $department;
                $dataArr[$key]['date'] = '<div class="hidden">'.$cart->getOrderAt().'</div>'.$orderedAt;

                if($history){
                    $status = Trans::make("Received");
                }
                else {
                    $status = Trans::make($cart->getStatusLabel());

                }
                $dataArr[$key]['status'] = $status;
                $dataArr[$key]['details'] = '<a href="/lab/order/orderdetails/'.$cart->getName().'">'.Trans::make('show more').'</a>';
            }
        }
        return json_encode(array('data' => $dataArr));
    }

    public function orderdetailsAction($orderName){

        $cart = OrderCart::findFirst("name LIKE '".$orderName."' AND organisation_id = ".$this->currentUser->getOrganisationId());

        if ($cart) {
            // mar log as opened
            $logChangedStatuses = LogLabOrderStatus::find(
                [
                    'conditions' => [
                        'order_id' => $cart->getId(),
                        'isopened' => false,
                    ],
                ]
            );

            foreach ($logChangedStatuses as $logChangedStatuse) {

                $logChangedStatuse->isopened = true;
                $logChangedStatuse->save();
            }
        }
        $this->view->order = $cart;
        $this->view->orderName = $cart->getName();
        $this->view->orderDate = Date::formatToDefault($cart->getOrderAt());
    }

    public function ajaxbuyedproductlistAction($orderName){

        $this->view->disable();

        $cart = OrderCart::findFirst(array("organisation_id = ".$this->currentUser->getOrganisationId()." AND name = '".$orderName."'", 'order' => 'id DESC'));
        $cartProducts = $cart->OrderCartProduct;
        $dataArr = array();

        foreach ($cartProducts as $key => $cartProduct){

            $product = Products::findFirst($cartProduct->getProductId());

            if((bool)$product){

                $amountMin = is_null($product->getAmountMin()) ? 1 : $product->getAmountMin();
                $dataArr[$key]['amount'] = $cartProduct->getAmount();
                $dataArr[$key]['name'] = $product->getName();
                $dataArr[$key]['material'] = $product->getMaterial();
                $dataArr[$key]['supplier'] = $cartProduct->Organisation->getName();
                $dataArr[$key]['project'] = is_null($cartProduct->getProjectNo()) ? '-' : $cartProduct->getProjectNo();
                $dataArr[$key]['code'] = $product->getCode();
                $dataArr[$key]['price'] = $product->getPrice();
                $actions = '';

                if ($cart->getStatus() < 4) {

                    $checked = '';

                    if ($cartProduct->getReceived() == 1) {

                        $checked = 'checked="checked"';
                    }
                    $actions = '<label class="received-label"><input cpid="' . $cartProduct->getId() . '" ' . $checked . ' type="checkbox" class="received" />' . Trans::make('received') . '</label>';
                }
                $dataArr[$key]['actions'] = $actions;
            }
            else {
                continue;
            }
        }
        return json_encode(array('data' => $dataArr));
    }

    public function ajaxreceivedAction($orderName){

        $this->view->disable();

        if ($this->request->isPost()) {

            $cart = OrderCart::findFirst(array("organisation_id = " . $this->currentUser->getOrganisationId() . " AND name = '" . $orderName . "'", 'order' => 'id DESC'));
            $cartProducts = $cart->OrderCartProduct;

            foreach ($cartProducts as $key => $cartProduct) {

                if ($this->request->getPost('id') == $cartProduct->getId()) {

                    $cartProduct->setReceived($this->request->getPost('received'));
                    $cartProduct->save();

                    return json_encode(array(
                        'result' => Trans::make('Changes have been saved'),
                        'type' => 'success',
                    ));
                }
            }
        }
        return json_encode(array(
            'result' => Trans::make('Changes have not been saved'),
            'type' => 'error',
        ));
    }

    public function ajaxreceivedallAction($orderName){

        $this->view->disable();

        if ($this->request->isPost()) {

            $cart = OrderCart::findFirst(array("organisation_id = " . $this->currentUser->getOrganisationId() . " AND name = '" . $orderName . "'", 'order' => 'id DESC'));
            $cartProducts = $cart->OrderCartProduct;

            foreach ($cartProducts as $key => $cartProduct) {

                if (in_array($cartProduct->getId(),$this->request->getPost('id')) ) {

                    $cartProduct->setReceived($this->request->getPost('received'));
                    $cartProduct->save();
                }
            }
            return json_encode(array(
                'result' => Trans::make('Changes have been saved'),
                'type' => 'success',
            ));
        }
        return json_encode(array(
            'result' => Trans::make('Changes have not been saved'),
            'type' => 'error',
        ));
    }

    public function ajaxmovetohistoryAction($orderName){

        $this->view->disable();

        if ($this->request->isPost()) {

            $success = true;

            $cart = OrderCart::findFirst(array("organisation_id = " . $this->currentUser->getOrganisationId() . " AND name = '" . $orderName . "'", 'order' => 'id DESC'));
            $cartProducts = $cart->OrderCartProduct;

            foreach ($cartProducts as $key => $cartProduct) {

                if ($cartProduct->getReceived() != 1) {

                    $success = false;
                    break;
                }
            }

            if ($success) {
                $cart->setStatus(4); // delivered
                $cart->save();

                return json_encode(array(
                    'result' => Trans::make('Changes have been saved'),
                    'type' => 'success',
                ));
            }
        }
        return json_encode(array(
            'result' => Trans::make('Not all products in this order have been received yet'),
            'type' => 'error',
        ));
    }
}
