<?php

namespace Signa\Controllers\Supplier;

use Signa\Models\OrderCart;
use Signa\Models\OrderCartProduct;
use Signa\Models\Products;
use Signa\Helpers\Date;
use Signa\Helpers\Translations as Trans;

class OrderController extends InitController
{
    public function indexAction(){

        $this->view->orders = OrderCart::find('deleted_at IS NULL AND status = 2 AND supplier_id = '.$this->currentUser->getOrganisationId().' ORDER BY created_at DESC');
        $this->view->disableSubnav = true;
    }

    public function editAction($id){

        if (!is_numeric($id)) {
            $message = [
                'type' => 'error',
                'content' => Trans::make('Order does not exist.')
            ];
            $this->session->set('message', $message);
            return $this->response->redirect($this->request->getServer('HTTP_REFERER'));
        }
        $this->view->disableSubnav = true;
        $this->db->query("UPDATE order_cart SET isopened = 1 WHERE deleted_at IS NULL AND id = {$id} AND status = 2 AND supplier_id = {$this->currentUser->getOrganisationId()} AND isopened IS NULL");

        $rules = array(
            sprintf(
                'deleted_at IS NULL AND id = %s AND status = 2 AND supplier_id = %s ORDER BY created_at DESC',
                $id,
                $this->currentUser->Organisation->getId()
            )
        );
        $order = OrderCart::findFirst($rules);

        if($order == null){

            $message = [
                'type' => 'error',
                'content' => Trans::make('Order does not exist.')
            ];
            $this->session->set('message', $message);
            return $this->response->redirect($this->request->getServer('HTTP_REFERER'));
        }
        $this->assets->collection('footer')
            ->addJs("js/app/cart.js");

        $this->view->orderName  = $order->getName();
        $this->view->order      = $order;
    }

    public function ajaxproductlistAction($id){

        $this->view->disable();

        $rules = array(
            sprintf(
                'deleted_at IS NULL AND id = %s AND status = 2 AND supplier_id = %s ORDER BY created_at DESC',
                $id,
                $this->currentUser->Organisation->getId()
            )
        );
        $order = OrderCart::findFirst($rules);

        if((bool)$order){
            $cartProducts = $order->OrderCartProduct;
        }
        $dataArr = array();

        if(isset($cartProducts) && count($cartProducts)){

            foreach ($cartProducts as $key => $cartProduct){

                if ($cartProduct->Organisation->getId() == $this->currentUser->Organisation->getId()) {

                    $product = Products::findFirst($cartProduct->getProductId());
                    $sent = $this->getSentCheckboxes($cartProduct->getSentAt(), $cartProduct->getId());

                    if ((bool)$product) {

                        $dataArr[$key]['amount'] = $cartProduct->getAmount();
                        $dataArr[$key]['name'] = $product->getName();
                        $dataArr[$key]['material'] = $product->getMaterial();
                        $dataArr[$key]['supplier'] = $cartProduct->Organisation->getName();
                        $dataArr[$key]['project'] = is_null($cartProduct->getProjectNo()) ? '-' : $cartProduct->getProjectNo();
                        $dataArr[$key]['code'] = $product->getCode();
                        $dataArr[$key]['price'] = $product->getPrice();
                        $dataArr[$key]['sent'] = $sent;
                    }
                    else {
                        continue;
                    }
                }
            }
        }
        return json_encode(array('data' => $dataArr));
    }

    public function ajaxbuyedproductlistAction($id){

        $this->view->disable();
        $order = OrderCart::findFirst('deleted_at IS NULL AND id = '.$id.' AND supplier_id = '.$this->currentUser->getOrganisationId().' ORDER BY created_at DESC');
        $cartProducts = $order->OrderCartProduct;
        $dataArr = array();

        foreach ($cartProducts as $key => $cartProduct){

            if ($cartProduct->Organisation == $this->currentUser->Organisation) {

                $product = Products::findFirst($cartProduct->getProductId());

                if ((bool)$product) {

                    $amountMin = is_null($product->getAmountMin()) ? 1 : $product->getAmountMin();
                    $dataArr[$key]['amount'] = $cartProduct->getAmount();
                    $dataArr[$key]['name'] = $product->getName();
                    $dataArr[$key]['material'] = $product->getMaterial();
                    $dataArr[$key]['supplier'] = $cartProduct->Organisation->getName();
                    $dataArr[$key]['project'] = is_null($cartProduct->getProjectNo()) ? '-' : $cartProduct->getProjectNo();
                    $dataArr[$key]['code'] = $product->getCode();
                    $dataArr[$key]['price'] = $product->getPrice();
                }
                else {
                    continue;
                }
            }
        }
        return json_encode(array('data' => $dataArr));
    }

    public function removeproductAction($id){

        $this->view->disable();
        $orderCartProduct = OrderCartProduct::findFirst($id);
        $orderConfirmed = OrderCart::findFirst('deleted_at IS NULL AND id = '.$orderCartProduct->OrderCart->getId().' AND status = 2 AND supplier_id = '.$this->currentUser->getOrganisationId().' ORDER BY created_at DESC');

        if ($orderConfirmed) {
            $status = $orderCartProduct->delete();
        }
        else {
            $status = false;
        }
        return json_encode(array('status' => $status));
    }

    public function completeorderAction($orderName){

        $this->view->disableSubnav = true;
        $order = OrderCart::findFirst("deleted_at IS NULL AND name = '".$orderName."' AND supplier_id = ".$this->currentUser->getOrganisationId()." ORDER BY created_at DESC");

        if($this->request->isPost()){

            $this->view->disable();

            // Close cart by changing status
            $order->setStatus(3);
            $order->setUpdatedAt(Date::currentDatetime());
            $order->setUpdatedBy($this->currentUser->getId());
            $order->save();

            $this->notifications->addNotification(array(
                'type' => 2,
                'subject' => Trans::make('Order update'),
                'description' => '<p>Your order '.$order->getName().' has been updated.</p>'
            ), null, $order->getOrganisationId());

            $post = $this->request->getPost();

            foreach ($post['data'] as $key => $postValue){

                $productCartId = str_replace('product-', '', $postValue['name']);
                $cartProduct = OrderCartProduct::findFirst($productCartId);
                $cartProduct->setAmount($postValue['value']);
                $cartProduct->save();
            }
            return json_encode(array('status' => true, 'redirect' => '/supplier/order/'));
        }

        $this->view->orderName  = $order->getName();
        $this->view->orderDate  = Date::formatToDefault($order->getOrderAt());
        $this->view->order      = $order;
    }

    public function historyAction(){

        $this->view->orders = OrderCart::find('deleted_at IS NULL AND status > 2 AND updated_by = '.$this->currentUser->getId().' ORDER BY created_at DESC');
        $this->view->disableSubnav = true;
    }

    public function historyDetailsAction($id){

        $order = OrderCart::findFirst($id);

        $this->view->order = $order;
        $this->view->disableSubnav = true;
    }

    public function ajaxProductSentStatusAction(){

        $this->view->disable();

        if($this->request->isAjax()){

            if($this->request->isPost()){

                $post = $this->request->getPost();
                $product = OrderCartProduct::findFirst($post['cartProdId']);
                $product->setSetAtAsCurrentDate();
                $status = $product->save();
                return $product->getSentAt();
            }
        };
    }

    private function getSentCheckboxes($sendAt, $cartProdId){

        $html = '';

        if(!is_null($sendAt)){

            $html .= '<input type="checkbox" value="sent" name="sent['.$cartProdId.']" data-sent="'.$cartProdId.'" checked disabled>';
            $html .= '<input type="hidden" class="hiddenSentStatus" name="realSent['.$cartProdId.']" data-sent="'.$cartProdId.'" value="'.$sendAt.'">';
        }
        else {
            $html .= '<input type="checkbox" value="sent" name="sent['.$cartProdId.']" data-sent="'.$cartProdId.'">';
            $html .= '<input type="hidden" class="hiddenSentStatus" name="realSent['.$cartProdId.']" data-sent="'.$cartProdId.'" value="0">';
        }
        return $html;
    }
}
