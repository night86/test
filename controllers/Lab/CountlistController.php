<?php

namespace Signa\Controllers\Lab;

use Signa\Models\Countlist;
use Signa\Models\CountlistProducts;
use Signa\Models\OrderShortlist;
use Signa\Helpers\Date;
use Signa\Models\Products;

class CountlistController extends InitController
{

    public function indexAction(){

        $this->assets->collection('footer')
            ->addJs("js/app/countlist.js")
            ->addJs("bower_components/datatables.net-buttons/js/dataTables.buttons.min.js")
            ->addJs("bower_components/datatables.net-buttons/js/buttons.flash.min.js")
            ->addJs("bower_components/datatables.net-buttons/js/buttons.html5.min.js")
            ->addJs("bower_components/datatables.net-buttons/js/buttons.print.min.js")
            ->addJs("bower_components/jszip/dist/jszip.min.js")
            ->addJs("bower_components/pdfmake/build/pdfmake.min.js")
            ->addJs("bower_components/pdfmake/build/vfs_fonts.js");
    }

    public function addAction(){

        $this->view->disable();

        $countlist = new Countlist();
        $countlist->createNew();

        if($countlist->save() !== false){

            $message = [
                'type' => 'success',
                'content' => 'New countlist has been created.',
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/lab/countlist/edit/'.$countlist->getId());
        }
        else {
            $message = [
                'type' => 'error',
                'content' => 'Cannot create new countlist.',
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/lab/countlist/');
        }
        return;
    }

    public function editAction($id){

        $countlist = Countlist::findFirst($id);

        $this->view->countlistStatus = $countlist->getStatus();
        $this->view->countlistId = $id;
    }

    public function saveAction($id){

        $countlist = Countlist::findFirst($id);
        $this->view->disable();

        if($this->request->isPost()){

            $post = $this->request->getPost();
            $parsedPost = $this->parsePostProducts($post['products']);

            if($post['operation'] == 'save'){

                $value = $this->saveProducts($parsedPost, $id);
                $countlist->setValue($value);
                $countlist->setStatus(2);
                $countlist->save();
            }
            else {
                $value = $this->saveProducts($parsedPost, $id);
                $countlist->complete($value);
                $countlist->save();
            }
        }
        return json_encode(array('status' => true, 'redirect' => '/lab/countlist/'));
    }

    public function ajaxcountlistAction(){

        $countlists = Countlist::find();
        $countlistsArr = array();

        foreach ($countlists as $key => $countlist){

            $countlistsArr[$key]['date'] = Date::formatToDefault($countlist->getCreatedAt());
            $countlistsArr[$key]['completedate'] = $countlist->getCompleteDate() ? $countlist->getCompleteDate() : '-';
            $countlistsArr[$key]['user'] = $countlist->CreatedBy->getFullName();
            $countlistsArr[$key]['value'] = $countlist->getValue() ? $countlist->getValue() : '-';
            $countlistsArr[$key]['action'] = '<a href="/lab/countlist/edit/'.$countlist->getId().'">'.$this->t->make('View').'</a>';
        }
        return json_encode(array('data' => $countlistsArr));
    }

    public function ajaxcountlistviewAction($id){

        $countlists = Countlist::findFirst($id);
        $shortlistsArr = array();

        if($countlists->getStatus() == 1){

            $organisationId = $this->currentUser->Organisation->getId();
            $shortlists = OrderShortlist::find('organisation_id = '.$organisationId);

            /**
             * @var int $key
             * @var OrderShortlist $shortlist
             */
            foreach ($shortlists as $key => $shortlist){

                $product = Products::getCurrentProduct($shortlist->getProductId());

                if ($product) {
                    $shortlistsArr[$key]['name'] = $product->getName();
                    $shortlistsArr[$key]['material'] = $product->getMaterial();
                    $shortlistsArr[$key]['supplier'] = $product->Organisation->getName();
                    $shortlistsArr[$key]['code'] = $product->getCode();
                    $shortlistsArr[$key]['price'] = $product->getPrice();
                    $shortlistsArr[$key]['amount'] = '<input type="number" min="0" value="0" name="product-'.$product->getId().'" />';
                }
            }
        }
        elseif ($countlists->getStatus() == 2){

            $countlistProducts = CountlistProducts::find('countlist_id = '.$id);
            $addedProductsCounter = 60000;

            foreach ($countlistProducts as $key => $countlistProduct){

                if($countlistProduct->getProductId() > 0){

                    $product = Products::findFirst($countlistProduct->getProductId());
                    $shortlistsArr[$key]['name'] = $product->getName();
                    $shortlistsArr[$key]['material'] = $product->getMaterial();
                    $shortlistsArr[$key]['supplier'] = $product->Organisation->getName();
                    $shortlistsArr[$key]['code'] = $product->getCode();
                    $shortlistsArr[$key]['price'] = $product->getPrice();
                    $shortlistsArr[$key]['amount'] = '<input type="number" min="0" value="'.$countlistProduct->getProductAmount().'" name="product-'.$product->getId().'" />';
                }
                else {
                    $shortlistsArr[$key]['name'] = '<input type="text" disabled name="product-name-'.$addedProductsCounter.'" value="'.$countlistProduct->getProductName().'" />';
                    $shortlistsArr[$key]['material'] = '-';
                    $shortlistsArr[$key]['supplier'] = '-';
                    $shortlistsArr[$key]['code'] = '-';
                    $shortlistsArr[$key]['price'] = '<input type="text" disabled name="product-price-'.$addedProductsCounter.'" value="'.$countlistProduct->getProductPrice().'" />';
                    $shortlistsArr[$key]['amount'] = '<input type="number" min="0" value="'.$countlistProduct->getProductAmount().'" name="product-amount-'.$product->getId().'" />';
                    $addedProductsCounter--;
                }
            }
        }
        elseif ($countlists->getStatus() == 3){

            $countlistProducts = CountlistProducts::find('countlist_id = '.$id);

            foreach ($countlistProducts as $key => $countlistProduct){

                if($countlistProduct->getProductId() > 0){

                    $product = Products::findFirst($countlistProduct->getProductId());
                    $shortlistsArr[$key]['name'] = $product->getName();
                    $shortlistsArr[$key]['material'] = $product->getMaterial();
                    $shortlistsArr[$key]['supplier'] = $product->Organisation->getName();
                    $shortlistsArr[$key]['code'] = $product->getCode();
                    $shortlistsArr[$key]['price'] = $product->getPrice();
                    $shortlistsArr[$key]['amount'] = $countlistProduct->getProductAmount();
                }
                else {
                    $shortlistsArr[$key]['name'] = $countlistProduct->getProductName();
                    $shortlistsArr[$key]['material'] = '-';
                    $shortlistsArr[$key]['supplier'] = '-';
                    $shortlistsArr[$key]['code'] = '-';
                    $shortlistsArr[$key]['price'] = $countlistProduct->getProductPrice();
                    $shortlistsArr[$key]['amount'] = $countlistProduct->getProductAmount();
                }
            }
        }
        return json_encode(array('data' => $shortlistsArr));
    }

    private function parsePostProducts($dataString){

        $addedProductsArr = array();
        $addedProductsCounter = 0;
        $productsArr = array();
        $productsCounter = 0;
        parse_str($dataString, $data);

        foreach ($data as $key => $value){

            preg_match('/product-[\d]+/', $key, $matches);

            if(count($matches)){

                $productId = str_replace('product-', '', $key);
                $productsArr[$productsCounter] = array('id' => (int)$productId, 'amount' => (int)$value);
                $productsCounter++;
            }
            else {
                $productAttribute = str_replace('product-', '', $key);
                preg_match('/[\w]+/', $productAttribute, $matchesAttribute);
                $productAttribute = $matchesAttribute[0];
                $addedProductsArr[$addedProductsCounter][$productAttribute] = $value;

                if($productAttribute == 'amount'){
                    $addedProductsCounter++;
                }
            }
        }
        return array('new_products' => $addedProductsArr, 'products' => $productsArr);
    }

    private function saveProducts($data, $countlistId){

        /*
         *  Clear count list rows to fill with new
         */
        $countlistProducts = CountlistProducts::find('countlist_id = '.$countlistId);

        foreach ($countlistProducts as $product){
            $product->delete();
        }

        $user = $this->currentUser;
        $value = 0;

        foreach ($data['products'] as $product){

            if($product['amount'] > 0){

                $productObj = Products::findFirst($product['id']);
                $countlistProduct = new CountlistProducts();
                $countlistProduct->setCountlistId($countlistId);
                $countlistProduct->setProductId($product['id']);
                $countlistProduct->setProductName($productObj->getName());
                $countlistProduct->setProductAmount($product['amount']);
                $countlistProduct->setProductPrice($productObj->getPrice());
                $countlistProduct->SetCreatedBy($user->getId());
                $countlistProduct->SetCreatedAt(Date::currentDatetime());
                $countlistProduct->save();

                $value += (int)$product['amount'] * (float)$productObj->getPrice();
            }
        }

        foreach ($data['new_products'] as $newProduct){

            if($newProduct['amount'] > 0){

                $countlistProduct = new CountlistProducts();
                $countlistProduct->setCountlistId($countlistId);
                $countlistProduct->setProductId(0);
                $countlistProduct->setProductName($newProduct['name']);
                $countlistProduct->setProductAmount($newProduct['amount']);
                $countlistProduct->setProductPrice($newProduct['price']);
                $countlistProduct->SetCreatedBy($user->getId());
                $countlistProduct->SetCreatedAt(Date::currentDatetime());
                $countlistProduct->save();

                $value += (int)$newProduct['amount'] * (float)$newProduct['price'];
            }
        }
        return $value;
    }
}
