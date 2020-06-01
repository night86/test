<?php

namespace Signa\Controllers\Signadens;

use Signa\Helpers\Translations;
use Signa\Models\LogLabPriceChange;
use Signa\Models\ProductCategories;
use Signa\Models\Products;
use Signa\Models\ImportProducts;
use Signa\Helpers\Import;
use Signa\Models\ImportMaps;
use Signa\Models\Purchase;
use Signa\Models\Users;
use Signa\Models\CodeTariffRanges;
use Signa\Models\CodeTariff;
use Signa\Helpers\Translations as Trans;

class ImportController extends InitController
{
    private $approvedObj;
    public $user;

//    public function beforeExecuteRoute(){
//
//        $currentAction = $this->di->getShared('dispatcher')->getActionName();
//
//        // List of id's products to approve
////        $this->approvedObj = $this->persistent->approve;
//        $this->user = $this->session->get('auth');
//
//        if (!is_array($this->approvedObj) && ($currentAction == 'categorize' || $currentAction == 'decline')) {
//
//            $message = [
//                'type' => 'error',
//                'content' => 'Import data is missing.'
//            ];
//            $this->session->set('message', $message);
//            $this->view->disable();
//            $this->response->redirect("signadens/import/");
//            return false;
//        }
//    }

    public function indexAction(){

        $import = ImportProducts::find('closed = 0');
        $importAr = [];

        foreach ($import as $imp) {
            $importAr[] = $imp;
        }

        $this->view->imports = $importAr;
        $this->view->disableSubnav = true;
    }

    public function approveAction($id){
        
        $import = ImportProducts::findFirst($id);
        $type = $import->getType();

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

        if ($this->request->isPost()) {

            $post = $this->request->getPost();

            // Approve
            if (isset($post['approve'])) {

                if(isset($post['selectedProducts'])){

                    $selectedProducts = $post['selectedProducts'];
                    $searchProducts = Products::find("id IN (".implode(',', $selectedProducts).")");

                    foreach($searchProducts as $p){

                        // If product does not have a tariff assigned
                        if($p->getTariffId() == NULL){

                            // If at least main product category assigned
                            if($p->getMainCategoryId() != NULL){

                                // Determine product category type
                                if($p->SubCategory){

                                    if($p->getSubSubCategoryId() != NULL){
                                        
                                        $productCategory = ProductCategories::findFirst($p->getSubSubCategoryId());
                                    }
                                    else {
                                        $productCategory = $p->SubCategory;
                                    }
                                }
                                else {
                                    $productCategory = $p->MainCategory;
                                }

                                // Search for ledger sales in product category or in product
                                if($p->getLedgerSalesId() != NULL || $productCategory->LedgerSales){

                                    // Search for tariff code ranges
                                    $existingRange = CodeTariffRanges::findFirst('manufacturer_id = '.$p->getManufacturerId().' AND product_category_id = '.$productCategory->getId());

                                    // If range exists for the combination of manufacturer and product category of this product
                                    if($existingRange != false){

                                        $searchLastTariff = CodeTariff::findFirst('code BETWEEN '.$existingRange->getRangeFrom().' AND '.$existingRange->getRangeTo().' ORDER BY code DESC');

                                        if($searchLastTariff != false){

                                            $newTariffCode = (int)$searchLastTariff->getCode() + 1;

                                            if($newTariffCode > $existingRange->getRangeTo()){

                                                $this->db->execute("UPDATE products SET approval_status = 'error_out' WHERE import_id = ".$id." AND id = ".$p->getId());
                                            }
                                            else {
                                                $newTariff = new CodeTariff();
                                                $newTariff->setProductId($p->getId());
                                                $newTariff->setCode($newTariffCode);
                                                $newTariff->setOrganisationId($this->currentUser->getOrganisationId());
                                                $newTariff->save();

                                                $this->db->execute("UPDATE products SET tariff_id = ".$newTariff->getId().", approval_status = NULL, approved = 1, active = 1 WHERE import_id = ".$id." AND id = ".$p->getId());
                                            }
                                        }
                                        else {
                                            $newTariffCode = (int)$existingRange->getRangeFrom();

                                            $newTariff = new CodeTariff();
                                            $newTariff->setProductId($p->getId());
                                            $newTariff->setCode($newTariffCode);
                                            $newTariff->setOrganisationId($this->currentUser->getOrganisationId());
                                            $newTariff->save();

                                            $this->db->execute("UPDATE products SET tariff_id = ".$newTariff->getId().", approval_status = NULL, approved = 1, active = 1 WHERE import_id = ".$id." AND id = ".$p->getId());
                                        }
                                    }
                                    else {
                                        $this->db->execute("UPDATE products SET approval_status = 'error_range' WHERE import_id = ".$id." AND id = ".$p->getId());
                                    }
                                }
                                else {
                                    $this->db->execute("UPDATE products SET approval_status = NULL, approved = 1 WHERE import_id = ".$id." AND id = ".$p->getId());
                                }
                            }
                            else {
                                $this->db->execute("UPDATE products SET approval_status = 'error_category' WHERE import_id = ".$id." AND id = ".$p->getId());
                            }
                        }
                        else {
                            $this->db->execute("UPDATE products SET approval_status = NULL, approved = 1, active = 1 WHERE import_id = ".$id." AND id = ".$p->getId());
                        }
                    }
                }
                else {
                    // No products
                    $this->view->disable();
                    $this->session->set('message', ['type' => 'warning', 'content' => 'Please select a product to approve.']);
                    $this->response->redirect("signadens/import/approve/" . $id);
                }
            }
            else {
                // Decline
                $this->view->disable();
                $this->response->redirect("signadens/import/decline/" . $id);
            }
        }
        $productsToImport = $import->productsToImport();

        if (count($productsToImport) === 0) {

//            $this->persistent->remove('approve');

            $import->checkProducts();

            $message = [
                'type' => 'success',
                'content' => 'Successfully approved products and send information to supplier.'
            ];
            $this->session->set('message', $message);
            $this->view->disable();
            $this->response->redirect("signadens/import/");
            return false;
        }

        // View vars and assets
        $this->view->productCategories = $final;
        $this->view->effectiveFrom = $import->getEffectiveFrom();
        $this->view->userFullName = $import->Created->getFullName();
        $this->view->products = $productsToImport;
        $this->view->type = $type;
        $this->view->id = $import->getId();
        $this->view->disableSubnav = true;
    }

    public function ajaxmissingcategoryAction(){

        $this->view->disable();

        if ($this->request->isPost()) {

            if($this->request->getPost('product_id') !== null && $this->request->getPost('product_category_id') !== null) {

                $product = Products::findFirst('id = '.$this->request->getPost('product_id'));
                $findCategory = ProductCategories::findFirst($this->request->getPost('product_category_id'));

                if($findCategory->Parent){

                    if($findCategory->Parent->Parent){

                        $product->setMainCategoryId($findCategory->Parent->Parent->getId());
                        $product->setSubCategoryId($findCategory->Parent->getId());
                        $product->setSubSubCategoryId($this->request->getPost('product_category_id'));
                    }
                    else {
                        $product->setMainCategoryId($findCategory->Parent->getId());
                        $product->setSubCategoryId($this->request->getPost('product_category_id'));
                    }
                }
                else {
                    $product->setMainCategoryId($this->request->getPost('product_category_id'));
                }
                $product->setApprovalStatus(NULL);
                $product->save();

                $result = [
                    "status"    => "ok",
                    "msg"       => Trans::make("Added product category")
                ];
            }
            else {
                $result = [
                    "status"    => "error",
                    "msg"       => Trans::make("Error while adding product category")
                ];
            }
            return json_encode($result);
        }
    }

    public function declineAction($id){

        $import = ImportProducts::findFirst($id);
        $selectedProducts = $this->persistent->approve;
        $type = $import->getType();

        if ($this->request->isPost()) {

            $post = $this->request->getPost();
            $productsArr = Import::decline($import, $post['message'], $post['messages']);

            $this->mongoLogger->createLog(
                array(
                    'datetime' => date('d-m-Y H:i:s'),
                    'page' => $this->router->getRewriteUri(),
                    'user' => $this->user->getEmail(),
                    'import_id' => $import->getId(),
                    'approval' => array(
                        'type' => 'declined',
                        'rows' => $productsArr,
                        'message' => $post['message']
                    )
                ),
                $this->user->getEmail());

            $this->notifications->addNotification(array(
                'type' => 2,
                'subject' => Trans::make('Import declined'),
                'description' => $this->declineNotificationContent($productsArr, $post['message'])
            ), null, $import->getSupplierId());

            $user = Users::findFirst($import->getCreatedBy());
            $this->persistent->remove('approve');

            $message = [
                'type' => 'success',
                'content' => 'Successfully declined products and send information to supplier.'
            ];
            $this->session->set('message', $message);
            $this->view->disable();

            if (count($import->productsToImport()) > 0) {
                $this->response->redirect("signadens/import/approve/" . $import->getId());
                return false;
            }

            $this->response->redirect("signadens/import/");
            return false;
        }

        $products = $import->selectedProductsToImport($selectedProducts, 'NOT');

        if (count($products) === 0) {

            $message = [
                'type' => 'error',
                'content' => 'There is no products to decline.'
            ];
            $this->session->set('message', $message);
            $this->view->disable();
            $this->response->redirect("signadens/import/approve/" . $id);
            return false;
        }

        $this->view->effectiveFrom = $import->getEffectiveFrom();
        $this->view->userFullName = $import->Created->getFullName();
        $this->view->products = $import->selectedProductsToImport($selectedProducts, 'NOT');
        $this->view->type = $type;
        $this->view->id = $import->getId();
    }

    public function categorizeAction($id){

        $import = ImportProducts::findFirst($id);
        $selectedProducts = $this->persistent->approve;

        $this->mongoLogger->createImportedProducts($selectedProducts);

        if ($import->getType() == 'create') {

            $approved = Import::fullImport($import, $selectedProducts);

            $this->mongoLogger->createLog(
                array(
                    'datetime' => date('d-m-Y H:i:s'),
                    'page' => $this->router->getRewriteUri(),
                    'user' => $this->user->getEmail(),
                    'import_id' => $import->getId(),
                    'approval' => array(
                        'type' => 'approved',
                        'rows' => $approved['products']
                    )
                ),
                $this->user->getEmail());

            $this->notifications->addNotification(array(
                'type' => 2,
                'subject' => Trans::make('Import approved'),
                'description' => $this->appproveNotificationContent()
            ), null, $import->getSupplierId());
        }
        $this->persistent->remove('approve');
        $message = [
            'type' => 'success',
            'content' => 'Successfully imported products.'
        ];
        $this->session->set('message', $message);
        $this->view->disable();
        return $this->response->redirect("signadens/import/");

    }

    private function declineNotificationContent($productsArr, $generalMessage){

        $html = '<p>' . Trans::make('Imported products has been declined with general message') . ':</p>';
        $html .= '<em><h4>' . $generalMessage . '</h4></em>';
        $productsWithMessagesArr = array();

        foreach ($productsArr as $product) {

            $productMessage = $product['decline_message'];

            if (!is_null($productMessage)) {
                $productsWithMessagesArr[] = '<strong>' . $product['name'] . '</strong> - <em>' . $productMessage . '</em>';
            }
        }

        if (count($productsWithMessagesArr) > 0) {

            $html .= '<p>'.Trans::make('with declined message for products').':</p>';

            foreach ($productsWithMessagesArr as $productWithMessage) {
                $html .= '<p>' . $productWithMessage . '</p>';
            }
        }
        return $html;
    }

    private function appproveNotificationContent($productsArr = null){

        $html = '<p>' . Trans::make('Your products have been approved') . ':</p>';

        return $html;
    }
}
