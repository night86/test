<?php

namespace Signa\Controllers\Lab;

use Signa\Libs\Products\ProductsData;
use Signa\Libs\Products\ProductsList;
use Signa\Models\OrderShortlist;
use Signa\Models\Products;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;
use Signa\Helpers\Translations as Trans;

class ShortlistController extends InitController
{
    public function indexAction(){

        $this->db->query("UPDATE order_shortlist SET isopened = 1 WHERE organisation_id = {$this->currentUser->getOrganisationId()} AND isopened IS NULL");

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
        $productEditContent = $this->getEditContent();
        $productMarginContent = $this->getMarginContent();

        $this->assets->collection('footerNotCompile')
            ->addJs("js/app/products.js")
            ->addJs("js/app/productsList.js?v=2")
            ->addJs("js/bootstrap/tooltip.js")
            ->addJs("bower_components/bootstrap3-typeahead/bootstrap3-typeahead.min.js");

        $this->view->filters = $filters;
        $this->view->editContent = $productEditContent;
        $this->view->marginContent = $productMarginContent;
    }

    public function markasviewedAction($id)
    {
        $this->db->query("UPDATE order_shortlist SET isopened = 1 WHERE id = {$id} AND organisation_id = {$this->currentUser->getOrganisationId()} AND isopened IS NULL");
        return $this->response->redirect($this->request->getHTTPReferer().'?shortlist=on');
    }

    public function deleteAction($id){

        /** @var OrderShortlist[] $shortlists */
        $shortlists = OrderShortlist::query()
            ->where('product_id = :product_id:')
            ->andWhere('organisation_id = :organisation_id:')
            ->bind([
                'product_id' => $id,
                'organisation_id' => $this->currentUser->getOrganisationId(),
            ])
            ->execute();

        /** @var OrderShortlist $shortlist */
        foreach ($shortlists as $shortlist) {

            $product = Products::getCurrentProduct($shortlist->getProductId());
            $result = $shortlist->delete();

            if ($result) {
                $product->save();
            }
        }

        $message = [
            'type' => 'success',
            'content' => 'Product has been deleted from shortlist.<br />Please note it will take a few minutes before the changes to your shortlist will be visible.',
        ];
        $this->session->set('message', $message);
        $this->response->redirect('/lab/shortlist/');
        $this->view->disable();
        return;
    }

    public function ajaxmarginAction(){

        $this->view->disable();

        if($this->request->isAjax()){

            $type = $this->request->getPost('type');
            $value = $this->request->getPost('value');
            $ids = $this->request->getPost('ids');
            $roundType = $this->request->getPost('roundType');
            $roundDirection = $this->request->getPost('roundDirection');

            if(count($ids) > 0){

                foreach ($ids as $shortlistId){

                    $shortlist = OrderShortlist::findFirst($shortlistId);
                    $shortlist->setMarginType($type);
                    $shortlist->setMarginValue($value);

                    if((int)$roundDirection > 0){
                        $shortlist->setRoundDirection($roundDirection);
                    }

                    if((int)$roundType > 0){
                        $shortlist->setRoundType($roundType);
                    }
                    $shortlist->save();
                }
            }
            else {
                $shortlists = OrderShortlist::find('organisation_id = '.$this->currentUser->getOrganisationId());

                foreach ($shortlists as $shortlist) {

                    $shortlist->setMarginType($type);
                    $shortlist->setMarginValue($value);
                    if((int)$roundDirection > 0)
                        $shortlist->setRoundDirection($roundDirection);
                    if((int)$roundType > 0)
                        $shortlist->setRoundType($roundType);
                    $shortlist->save();
                }
            }
            $message = [
                'type' => 'success',
                'content' => 'Margin values has been updated.',
            ];
            $this->session->set('message', $message);
            return json_encode(array('status' => true));
        }
    }

    public function ajaxproductamountAction($id){

        /** @var OrderShortlist[] $shortlists */
        $shortlists = OrderShortlist::query()
            ->where('product_id = :product_id:')
            ->andWhere('organisation_id = :organisation_id:')
            ->bind([
                'product_id' => $id,
                'organisation_id' => $this->currentUser->getOrganisationId(),
            ])
            ->execute();

        if (count($shortlists) > 0) {
            $orderShortlist = $shortlists[0];
        }
        else {
            return [];
        }

        $amount = $orderShortlist->getAmountMin();
        $amountMin = Products::findFirst($orderShortlist->getProductId())->getAmountMin();
        $amountMin = is_null($amountMin) ? 1 : $amountMin;
        $marginType = $orderShortlist->getMarginTypeValue();
        $marginValue = $orderShortlist->getMarginValue();
        $roundDirection = $orderShortlist->getRoundDirection();
        $roundType = $orderShortlist->getRoundType();
        $returnArr = array(
            'amount_min' => $amountMin,
            'amount' => $amount,
            'margin_type' => $marginType,
            'margin_value' => $marginValue,
            'round_direction' => $roundDirection,
            'round_type' => $roundType,
        );
        return json_encode($returnArr);
    }

    public function ajaxsaveamountAction($id){

        /** @var OrderShortlist[] $shortlists */
        $shortlists = OrderShortlist::query()
            ->where('product_id = :product_id:')
            ->andWhere('organisation_id = :organisation_id:')
            ->bind([
                'product_id' => $id,
                'organisation_id' => $this->currentUser->getOrganisationId(),
            ])
            ->execute();

        if (count($shortlists) > 0) {
            $orderShortlist = $shortlists[0];
        }
        else {
            return json_encode(array('status' => false));
        }

        $orderShortlist->setAmountMin($this->request->getPost('amount'));
        $orderShortlist->setMarginType($this->request->getPost('margin_type'));
        $orderShortlist->setMarginValue($this->request->getPost('margin_value'));
        $orderShortlist->setRoundDirection($this->request->getPost('round_direction'));
        $orderShortlist->setRoundType($this->request->getPost('round_type'));
        $status = $orderShortlist->save();
        return json_encode(array('status' => $status));
    }

    private function getEditContent(){

        $html = '<a href="#" class="btn btn-danger delete" style="margin-top: 15px;" id="shortlistDeleteProduct">'.Trans::make("Remove from shortlist").'</a>';
        return $html;
    }

    private function getMarginContent(){

        $marginTypes = array(
            1 => 'Fixed price',
            2 => 'Fixed margin in euro',
            3 => 'As percentages of the purchase price',
            4 => 'As percentages of the sales price',
        );
        $roundDirection = array(
            1 => Trans::make('Up'),
            2 => Trans::make('Down'),
        );
        $roundType = array(
            1 => Trans::make('Decimal'),
            2 => Trans::make('Integer'),
        );

        $html = '<label>'.Trans::make("Margin type").'</label><select name="margin_type" class="form-control" id="setMarginType">';

        foreach ($marginTypes as $key => $marginType){

            $html .= '<option value="'.$key.'">'.$marginType.'</option>';
        }
        $html .= '</select>';
        $html .= '<label>'.Trans::make("Margin value/percentage").'</label><input type="text" class="form-control" id="setMarginValue" name="margin_value" />';
        $html .= '<label>'.Trans::make("Round direction").'</label><select name="round_direction" class="form-control" id="setRoundDirection">';
        $html .= '<option value=""></option>';

        foreach ($roundDirection as $key => $value){

            $html .= '<option value="'.$key.'">'.$value.'</option>';
        }
        $html .= '</select>';
        $html .= '<label>'.Trans::make("Round type").'</label><select name="round_type" class="form-control" id="setRoundType">';
        $html .= '<option value=""></option>';

        foreach ($roundType as $key => $value){

            $html .= '<option value="'.$key.'">'.$value.'</option>';
        }
        $html .= '</select>';
        return $html;
    }

    public function ajaxnamessimpleAction(){

        if($this->request->isAjax()){

            $this->view->disable();

            $pd = new ProductsData();
            $pd->setShortlist(true);
            $pd->setAutosuggest(true);
            $data = $pd->getFilteredProducts();

            echo json_encode($data); die;
        }
    }

    private static function urlFromParams($params){

        $url = '';

        foreach ($params as $categoryName => $category){

            foreach ($category as $key => $value){

                $url .= '&filter['.$categoryName.']['.$key.']='.$value;
            }
        }
        return $url;
    }
}
