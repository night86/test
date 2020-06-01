<?php

namespace Signa\Controllers\Lab;

use Signa\Helpers\Translations;
use Signa\Models\OrderCart;
use Signa\Models\OrderCartProduct;
use Signa\Models\Products;
use Signa\Helpers\Date;
use Signa\Models\Organisations;
use Signa\Helpers\Translations as Trans;
use Signa\Models\SupplierInfo;
use Signa\Models\Users;

class CartController extends InitController
{
    public function indexAction(){

        $cart = OrderCart::findFirst("status = 1 AND organisation_id = ".$this->currentUser->getOrganisationId());

        if($cart == null || count($cart->OrderCartProduct) === 0){

            $message = [
                'type' => 'error',
                'content' => 'Your cart is empty.',
            ];
            $this->session->set('message', $message);
            return $this->response->redirect($this->request->getServer('HTTP_REFERER'));
        }
        $cartProducts = $cart->OrderCartProduct;
        $suppliersArr = array();
        $suppliersLogoArr = array();

        foreach ($cartProducts as $cartProduct){

            if(!in_array($cartProduct->Organisation->getName(), $suppliersArr)){

                $suppliersArr[] = $cartProduct->Organisation->getName();
                /** @var SupplierInfo $supplierInfo */
                $supplierInfo = SupplierInfo::findByOrganisation($cartProduct->Organisation->getId());

                if($supplierInfo !== false){
                    $texts[trim($cartProduct->Organisation->getName())] = sprintf(Translations::make("Delivery time is %s business day when ordered before %s"), $supplierInfo->getDeliveryWorkdays(), \DateTime::createFromFormat('H:i:s', $supplierInfo->getDeliveryTime())->format('H:i')) . '<br>' . $supplierInfo->getText();
                }

                if($cartProduct->Organisation->getLogo()){
                    $suppliersLogoArr[$cartProduct->Organisation->getName()] = '/uploads/images/organisation/'.$cartProduct->Organisation->getLogo();
                }
            }
        }
        $this->view->suppliers = $suppliersArr;
        $this->view->suppliersLogo = $suppliersLogoArr;
        $this->view->suppliersTexts = json_encode($texts);
        $this->view->orderName = $cart->getName();
    }

    public function ajaxproductlistAction(){

        $this->view->disable();

        $cart = OrderCart::findFirst("status = 1 AND organisation_id = ".$this->currentUser->getOrganisationId());

        if((bool)$cart){
            $cartProducts = $cart->OrderCartProduct;
        }
        $dataArr = array();

        if(isset($cartProducts) && count($cartProducts)){

            foreach ($cartProducts as $key => $cartProduct){

                /** @var Products $product */
                $product = Products::findFirst($cartProduct->getProductId());

                if((bool)$product){

                    $user = Users::findFirst($cartProduct->getCreatedBy());
                    $department = (is_null($user->getDepartmentName())) ?  '-' : $user->getDepartmentName();
                    $amountMin = is_null($product->getAmountMin()) ? 1 : $product->getAmountMin();
                    $dataArr[$key]['amount'] = '<input type="number" name="product-'.$cartProduct->getId().'" class="product-amount" value="'.$cartProduct->getAmount().'" min="'.$amountMin.'" />';

                    if($product->getSpecialOrder() == '1'){

                        $dataArr[$key]['name'] = $product->getName().' <img src="/public/images/products/special_product_icon.png" width="30" />';
                    }
                    else {
                        $dataArr[$key]['name'] = $product->getName();
                    }

                    $dataArr[$key]['delivery_time'] = $product->getDeliveryTime();
                    $dataArr[$key]['supplier'] = $cartProduct->Organisation->getName();
                    $dataArr[$key]['orderedby'] = $cartProduct->CreatedBy->getFullName();
                    $dataArr[$key]['department'] = $department;

                    if ($this->currentUser->hasRole('ROLE_LAB_USER_EDIT')) {

                        $dataArr[$key]['project'] = '<input type="text" name="product-no-'.$cartProduct->getId().'" class="product-no" value="'.$cartProduct->getProjectNo().'" />';
                    }
                    else {
                        $dataArr[$key]['project'] = is_null($cartProduct->getProjectNo()) ? '-' : $cartProduct->getProjectNo();
                    }
                    $dataArr[$key]['code'] = $product->getCode();
                    $dataArr[$key]['price'] = $product->calculateDiscount($cartProduct->getPrice());
                    $dataArr[$key]['delete'] = '<a href="/lab/cart/removeproduct/'.$cartProduct->getId().'" class="btn btn-danger delete"><i class="pe-7s-trash"></i> '.$this->t->make('Delete').'</a>';
                }
                else {
                    continue;
                }
            }
        }
        return json_encode(array('data' => $dataArr));
    }

	public function ajaxsuppliertextAction()
	{
		$this->view->disable();

		$text = '';

		if ($this->request->isPost())
		{
			$organisation = Organisations::query()
			                             ->where('name LIKE(\':name:\')')
			                             ->bind(['name' => $this->request->get('supplierName')])
			                             ->execute();
			$text = SupplierInfo::getTextByOrganisationId($organisation->id);

		}
		return json_encode(array('text' => $text));
	}

    public function removeproductAction($id){

        $this->view->disable();

        $orderCartProduct = OrderCartProduct::findFirst($id);
        $status = $orderCartProduct->delete();

        return json_encode(array('status' => $status));
    }

    public function completeorderAction($orderName){

        $cart = OrderCart::findFirst("name LIKE '".$orderName."' AND organisation_id = ".$this->currentUser->getOrganisationId());

        if($this->request->isPost()){

            $this->view->disable();
            $post = $this->request->getPost();
            $supplier = Organisations::findFirst("name = '".$post['supplier']."'");

            $changeCart = false;
            $currentCart = '';

            if(count($cart->productsSuppliers()) > 1){

                // Close cart by changing status
                $splittedCart = new OrderCart();
                $splittedCart->createNew();
                $splittedCart->setSupplierId($supplier->getId());
                $splittedCart->setStatus(2);
                $splittedCart->setOrderAt(Date::currentDatetime());
                $splittedCart->setOrderBy($this->currentUser->getId());
                $splittedCart->save();

                $currentCart = $splittedCart;
                $cartCode = $splittedCart->getName();
                $changeCart = true;

                $this->notifications->addNotification(array(
                    'type' => 1,
                    'subject' => Trans::make('New order'),
                    'description' => $this->orderNotificationContent($splittedCart),
                ),null, $supplier->getId());
            }
            else {

                // Close cart by changing status
                $cart->setSupplierId($supplier->getId());
                $cart->setStatus(2);
                $cart->setOrderAt(Date::currentDatetime());
                $cart->setOrderBy($this->currentUser->getId());
                $cart->save();

                $currentCart = $cart;
                $cartCode = $cart->getName();
            }

            foreach ($post['data'] as $key => $postValue){

                $productCartId = str_replace('product-', '', $postValue['name']);
                $cartProduct = OrderCartProduct::findFirst($productCartId);

                if($changeCart){
                    $cartProduct->setOrderCartId($currentCart->getId());
                }

                if(!empty($postValue['project'])){
                    $cartProduct->setProjectNo($postValue['project']);
                }
                $cartProduct->setAmount($postValue['value']);
                $cartProduct->save();
            }
            $updatedCart = OrderCart::findFirst("name LIKE '".$cartCode."' AND organisation_id = ".$this->currentUser->getOrganisationId());

            $this->notifications->addNotification(array(
                'type' => 1,
                'subject' => Trans::make('New order'),
                'description' => $this->orderNotificationContent($updatedCart),
            ),null, $supplier->getId());

            $savedArr = $this->mail->sendOrganisationAdmins($supplier, Trans::make("New order")." ".$cartCode, 'newOrder', array('cart' => $updatedCart, 'orderCartProducts' => $updatedCart->OrderCartProduct, 'cartCode' => $cartCode));
            $sendEmail = $this->mail->send($supplier->getEmail(), Trans::make("New order")." ".$cartCode, 'newOrder', array('cart' => $updatedCart, 'orderCartProducts' => $updatedCart->OrderCartProduct, 'cartCode' => $cartCode));
            $result = json_encode(array('status' => true, 'redirect' => $post['redirectUrl'].$cartCode));

            return $result;
        }
        $this->view->orderName = $cart->getName();
        $this->view->orderDate = Date::formatToDefault($cart->getOrderAt());
    }

    public function saveorderAction($orderName){

        $cart = OrderCart::findFirst("name LIKE '".$orderName."' AND organisation_id = ".$this->currentUser->getOrganisationId());

        if($this->request->isPost()){

            $this->view->disable();
            $post = $this->request->getPost();

            foreach ($post['data'] as $key => $postValue){

                $productCartId = str_replace('product-', '', $postValue['name']);
                $productCartId = str_replace('no-', '', $productCartId);
                $fn = $postValue['fn'];

                $cartProduct = OrderCartProduct::findFirst($productCartId);
                $cartProduct->$fn($postValue['value']);
                $cartProduct->save();
            }
            return json_encode(array('status' => true, 'message' => Trans::make('Order has been updated.')));
        }
        return json_encode(array('status' => false));
    }

    public function ajaxbuyedproductlistAction($orderName){

        $this->view->disable();

        $cart = OrderCart::findFirst(array("status = 2 AND organisation_id = ".$this->currentUser->getOrganisationId()." AND name = '".$orderName."'", 'order' => 'id DESC'));
        $cartProducts = $cart->OrderCartProduct;
        $dataArr = array();

        foreach ($cartProducts as $key => $cartProduct){

            $product = Products::findFirst($cartProduct->getProductId());

            if((bool)$product){

                $dataArr[$key]['amount'] = $cartProduct->getAmount();
                $dataArr[$key]['name'] = $product->getName();
                $dataArr[$key]['delivery_time'] = $product->getDeliveryTime();
                $dataArr[$key]['supplier'] = $cartProduct->Organisation->getName();
                $dataArr[$key]['project'] = is_null($cartProduct->getProjectNo()) ? '-' : $cartProduct->getProjectNo();
                $dataArr[$key]['code'] = $product->getCode();
                $dataArr[$key]['price'] = $product->getPrice();
            }
            else {
                continue;
            }
        }
        return json_encode(array('data' => $dataArr));
    }

    private function orderNotificationContent(OrderCart $cart){

        $orderCartProducts = $cart->OrderCartProduct;
        $totalPrice = 0;
        $html = Trans::make("You've received the following order from lab").$cart->Organisation->getName();
        $html .= '<table class=&quot;table table-striped&quot;>';
        $html .= '<thead><tr><th>'.Trans::make("Amount").'</th><th>'.Trans::make("Product name").'</th><th>'.Trans::make("Product material").'</th><th>'.Trans::make("Product code").'</th><th>'.Trans::make("Product price").'</th></tr></thead>';
        $html .= '<tbody>';

        /** @var OrderCartProduct $orderCartProduct */
        foreach ($orderCartProducts as $orderCartProduct){

            $product = Products::getCurrentProduct($orderCartProduct->getProductId());

            if ($product) {
                $totalPrice += $product->getPrice() * $orderCartProduct->getAmount();
                $html .= '<tr><td>'.$orderCartProduct->getAmount().'</td><td>'.$product->getName().
                    '</td><td>'.$product->getMaterial().'</td><td>'.$product->getCode().
                    '</td><td>&euro;'.$product->getPrice().'</td></tr>';
            }
        }
        $html .= '<tr><td></td><td></td><td></td><td></td><td></td></tr>';
        $html .= '<tr><td></td><td></td><td></td><td>'.Trans::make("Total Ex. VAT").'</td><td>&euro;'.$totalPrice.'</td></tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= Trans::make("Open this order in").' <a href=&quot;/supplier/order/edit/'.$cart->getId().'&quot;>'.Trans::make("Orderlist").'</a>';

        return $html;
    }
}
