<?php

namespace Signa\Controllers\Dentist;

use Signa\Helpers\Translations;
use Signa\Models\CategoryTree;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;
use Signa\Models\CategoryTreeRecipes;
use Signa\Models\CodeTariff;
use Signa\Models\DentistGroupDiscount;
use Signa\Models\DentistOrder;
use Signa\Models\DentistOrderBsn;
use Signa\Models\DentistOrderData;
use Signa\Models\DentistOrderRecipe;
use Signa\Models\DentistOrderRecipeData;
use Signa\Models\DentistOrderFile;
use Signa\Models\DentistOrderNotes;
use Signa\Models\DentistOrderRecipeDelivery;
use Signa\Models\DentistOrderRecipeDataOptions;
use Signa\Models\DentistOrderRecipeFile;
use Signa\Models\MapLabTariffLedger;
use Signa\Models\Organisations;
use Signa\Models\LabDentists;
use Signa\Models\Products;
use Signa\Models\RecipeActivity;
use Signa\Models\RecipeCustomField;
use Signa\Models\RecipeCustomFieldOptions;
use Signa\Models\RecipeDefaultSetting;
use Signa\Models\Recipes;
use Signa\Models\RecipeStatus;
use Signa\Libs\Recipes as RecipesLib;
use Signa\Helpers\Date;
use Signa\Helpers\Translations as Trans;
use Signa\Models\RecipeStatusTime;
use Signa\Models\Users;
use Signa\Models\DentistLocation;

class OrderController extends InitController
{
    private $breadCrumb = [];

    private $existingchild = false;

    public function indexAction(){

        $filters = null;
        $dentistUser = Users::findfirst("id = ".$this->currentUser->getId());

        /*if($dentistUser->getMainLocationId() != NULL){

            $orders = DentistOrder::find('deleted_at IS NULL AND status = 1 AND dentist_id = '.$this->currentUser->getOrganisationId().' AND location_id = '.$dentistUser->getMainLocationId().' ORDER BY created_at DESC');
        }
        else {*/
            $orders = DentistOrder::find('deleted_at IS NULL AND status = 1 AND dentist_id = '.$this->currentUser->getOrganisationId().' ORDER BY created_at DESC');
        //}

        // Save form data
        if ($this->request->isPost()) {

            // Post var allocation
            $location = $this->request->getPost('location');

            foreach ($location as $k => $v){

                if($v == 1){
                    $filters[] = $k;
                }
            }

            // Location filters
            if($filters != NULL){
                $orders = DentistOrder::find('deleted_at IS NULL AND status = 1 AND dentist_id = '.$this->currentUser->getOrganisationId().' AND location_id IN('.implode(",",$filters).') ORDER BY created_at DESC');
            }
        }

//        _dump($filters);exit();
        $this->view->dentistUser = $dentistUser;
        $this->view->filters = $filters;
        $this->view->orders = $orders;
        $this->view->locations = DentistLocation::find('dentist_id = '.$this->currentUser->getOrganisationId());
        $this->view->disableSubnav = true;
    }

    public function createAction(){

        $order = new DentistOrder();

        // Check if lab is creating the order or dentist
        if($this->currentUser->Organisation->getOrganisationTypeId() == 4){
            $order->setLabCreated($this->currentUser->getOrganisationId());
        }
        $order->save();

        $orderBsn = new DentistOrderBsn();
        $orderBsn->setOrderId($order->getId());
        $orderBsn->save();

        $orderData = new DentistOrderData();
        $orderData->setOrderId($order->getId());
        $orderData->save();

        $this->response->redirect(sprintf('/dentist/order/edit/%s', $order->getCode()));
        $this->view->disable();
    }

    public function deleteAction($code){

        $order = DentistOrder::findFirst(
            array(
                'deleted_at IS NULL AND dentist_id = :dentist: AND code = :code:',
                'bind' => array(
                    'dentist' => $this->currentUser->Organisation->getId(),
                    'code' => $code
                )
            )
        );

        if ($order) {
            $order->softDelete();
            $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Order has been deleted.')));
        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Order doesn\'t exist.')));
        }
        $this->response->redirect('/dentist/order/');
        $this->view->disable();
        return;
    }

    public function deleterecipeAction(){

        if($this->request->isAjax() && $this->request->isPost()){

            $dentistRecipe = DentistOrderRecipe::findFirst('id='.$this->request->getPost('recipeId'));
            $dentistRecipe->setDeletedAt(date("Y-m-d H:i:s"));
            $dentistRecipe->setDeletedBy($this->currentUser->getOrganisationId());
            $dentistRecipe->save();

            if($dentistRecipe->save()){
                $result = [
                    "msg" => Trans::make('Recipe deleted'),
                    "status" => "ok"
                ];
            }
            else {
                $result = [
                    "msg" => Trans::make('Error when deleting the recipe'),
                    "status" => "error"
                ];
            }
            return json_encode($result);
        }
    }

    public function completeAction($code){

        $order = DentistOrder::findFirst('deleted_at IS NULL AND code = '.$code);

        if ($order) {
            $order->setStatus(2);
            $order->setOrderAt(Date::currentDatetime());
            $order->save();

            $this->notifications->addNotification(array(
                'type' => 2,
                'subject' => Trans::make('New order'),
                'description' => $this->orderNotificationContent($order)
            ), null, $order->Recipes[0]->Lab->getId());

            $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Order has been completed.')));
            $this->response->redirect('/dentist/order/inprogress');

        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Order doesn\'t exist.')));
            $this->response->redirect('/dentist/order/');
        }
        $this->view->disable();
    }

    public function packingpdfprintAction($code){

        $order = DentistOrder::findFirst('deleted_at IS NULL AND dentist_id = '.$this->currentUser->getOrganisationId().' AND code = '.$code);
        $lab = $order->DentistOrderRecipe[0]->Recipes->Lab;
        $dentist = $this->currentUser->Organisation;

        $this->view->render('print', 'packing_slip_print', [
            'lab' => $lab,
            'dentist' => $dentist,
            'order' => $order
        ]);
    }

    public function packingpdfAction($code){

        $this->view->disable();

        $order = DentistOrder::findFirst('deleted_at IS NULL AND dentist_id = '.$this->currentUser->getOrganisationId().' AND code = '.$code);
        $header_html = $this->view->getRender('pdf', 'header');

        $html = $this->view->getRender('pdf', 'packing_slip', [
            'lab' => $order->DentistOrderRecipe[0]->Recipes->Lab,
            'dentist' => $this->currentUser->Organisation,
            'order' => $order
        ]);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'tempDir' => $this->config->application->cacheDir,
            'setAutoTopMargin' => 'stretch'
        ]);

        $stylesheet1 = file_get_contents('css/pdf/bootstrap.css');
        $stylesheet2 = file_get_contents('css/pdf/bootstrap-theme.min.css');
        $stylesheet3 = file_get_contents('css/pdf/style.css');

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle(Translations::make('Packing slip'));
        $mpdf->SetAuthor("Signadens");
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetHTMLHeader($header_html);
        $mpdf->WriteHTML($stylesheet1.$stylesheet2.$stylesheet3,1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output("order_".$order->getCode().'.pdf', "D");
    }

    public function printlabelAction($code){

        $this->view->disable();
        $order = DentistOrder::findFirst('deleted_at IS NULL AND dentist_id = '.$this->currentUser->getOrganisationId().' AND code = '.$code);

        $lab = $order->DentistOrderRecipe[0]->Recipes->Lab;
        $dentist = $this->currentUser->Organisation;
        $html = $this->view->getRender('pdf', 'print_label', [
            'lab' => $lab,
            'dentist' => $dentist,
            'order' => $order
        ]);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [89,36],
            'tempDir' => $this->config->application->cacheDir,
            'setAutoTopMargin' => 'stretch'
        ]);
        $mpdf->SetHeader(null);

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle(Translations::make('Label'));
        $mpdf->SetAuthor("Signadens");
        $mpdf->WriteHTML($html);
        $mpdf->Output("label_".$order->getCode().'.pdf', "D");
    }

    public function printlabelprintAction($code){

        $order = DentistOrder::findFirst('deleted_at IS NULL AND dentist_id = '.$this->currentUser->getOrganisationId().' AND code = '.$code);

        $this->view->render('print', 'print_label_print', [
            'lab' => $order->DentistOrderRecipe[0]->Recipes->Lab,
            'dentist' => $this->currentUser->Organisation,
            'order' => $order
        ]);
    }

    public function editAction($code = NULL){

        // If code is null, redirect
        if ($code == NULL) {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Order doesn\'t exist.')));
            $this->response->redirect('/dentist/order/');
            $this->view->disable();
            return;
        }

        $files = [];

        $field = ($this->currentUser->Organisation->getOrganisationTypeId() == 4) ? "lab_created" : "dentist_id";
        $order = DentistOrder::findFirst($field.' = '.$this->currentUser->getOrganisationId().' AND code = '.$code);
        $orderRecipes = DentistOrderRecipe::find('order_id = '.$order->getId().' AND deleted_at IS NULL AND deleted_by IS NULL');

        if (!$order) {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Order doesn\'t exist.')));
            $this->response->redirect('/dentist/order/');
            $this->view->disable();
            return;
        }

        // If order is already set for delivery or delivered cannot access edit
        if (in_array($order->getStatus(),[4,5])) {

            $this->response->redirect('/dentist/order/details/'.$code);
            $this->view->disable();
            return;
        }
        $deliveryTime = [0];
        $contactDetails = [];

        foreach ($order->DentistOrderRecipe as $recipe) {

            $name = $recipe->Recipes->Lab->getName();
            $phone = $recipe->Recipes->Lab->getTelephone();
            $cd = [
                "name" => $name,
                "phone" => $phone
            ];

            if (!in_array($cd, $contactDetails)) {
                $contactDetails[] = $cd;
            }

            $dt = $recipe->Recipes->getDeliveryTime();

            if ($dt === null) {
                $deliveryTime[] = 0;
            }
            else {
                $deliveryTime[] = $dt;
            }
        }
        $deliveryTime = max($deliveryTime);

        // Save form data
        if ($this->request->isPost()) {

            $post = $this->request->getPost();
            $postData = $this->request->getPost('data');
            $orderNoteId = null;

            $order->DentistOrderData->setPatientInitials($postData['patient_initials']);
            $order->DentistOrderData->setPatientInsertion($postData['patient_insertion']);
            $order->DentistOrderData->setPatientLastname($postData['patient_lastname']);
            $order->DentistOrderData->setPatientGender($postData['patient_gender']);
            $order->DentistOrderData->setPatientNumber($postData['patient_number']);

            if($postData['patient_birth']['year'] != "-" && $postData['patient_birth']['month'] != "-" && $postData['patient_birth']['day'] != "-"){

                $order->DentistOrderData->setPatientBirth($postData['patient_birth']['year']."-".$postData['patient_birth']['month']."-".$postData['patient_birth']['day']);
            }
            else {
                $order->DentistOrderData->setPatientBirth(NULL);
            }
            $order->DentistOrderBsn->setBsn((!empty($this->request->getPost('bsn')) && $this->request->getPost('bsn') != 0) ? $this->request->getPost('bsn') : NULL);
            $order->setDentistUserId((!empty($post['dentist_user_id'])) ? $post['dentist_user_id'] : NULL);
            $order->setLocationId((!empty($post['location_id'])) ? $post['location_id'] : NULL); //DISABLED FOR NOW
            $order->save();

            if (!empty($this->request->getPost('description'))) {

                $orderNotes = new DentistOrderNotes();
                $orderNotes->setNote($this->request->getPost('description'));
                $orderNotes->setOrderId($order->getId());
                $orderNotes->save();
                $orderNoteId = $orderNotes->getId();
            }

            if ($this->request->hasFiles() == true) {

                $configDir = $this->config->application->dentistOrderDir;

                // Print the real file names and their sizes
                foreach ($this->request->getUploadedFiles() as $file) {

                    $imgDir = $configDir;

                    if (!is_dir($imgDir)) {
                        mkdirR($imgDir);
                    }

                    // To avoid sending empty files, check if file has name
                    if (!empty($file->getName())) {
                        $file->moveTo($configDir . $file->getName());
                        $orderFile = new DentistOrderFile();
                        $orderFile->setOrderId($order->getId());
                        $orderFile->setFileName($file->getName());
                        $orderFile->setFilePath($configDir);
                        $orderFile->setFileType($file->getType());

                        if (!is_null($orderNoteId))
                            $orderFile->setOrderNoteId($orderNoteId);
                        $orderFile->save();
                    }
                }
            }

            if (isset($post['complete'])){

                return $this->response->redirect('/dentist/order/complete/' . $code);
            }
            elseif($post['reload_after'] == 1){

                if(isset($post['lab_choice']) && $post['lab_choice'] != ""){

                    return $this->response->redirect('/dentist/order/add/'.$code.'/0/'.$post['lab_choice']);
                }
                else {
                    return $this->response->redirect('/dentist/order/add/' . $code);
                }
            }
            $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Order has been updated.')));
        }

        $files = DentistOrderFile::find(array(
            'deleted_at IS NULL AND order_id = :orderid:',
            'bind' => array(
                'orderid' => $order->getId()
            ),
            'columns' => ['file_name', 'id']
        ))->toArray();

        if($order->DentistOrderData->patient_birth != NULL){
            $birthDate = [
                "day" => date("d", strtotime($order->DentistOrderData->patient_birth)),
                "month" => date("m", strtotime($order->DentistOrderData->patient_birth)),
                "year" => date("Y", strtotime($order->DentistOrderData->patient_birth))
            ];
        }

        // View vars and assets
        $this->assets->collection('footer')
            ->addJs("bower_components/moment/min/moment.min.js")
            ->addJs("js/app/orderFile.js");

        // If lab is ordering, get available dentists, or viceversa
        if($this->currentUser->Organisation->getOrganisationTypeId() == 4){

            $this->view->labDentists = LabDentists::find('lab_id = '.$this->currentUser->getOrganisationId());
            $this->view->lastLocation = DentistOrder::findFirst('lab_created = '.$order->getLabCreated().' AND location_id IS NOT NULL AND order_at IS NOT NULL ORDER BY order_at DESC');
        }
        elseif($this->currentUser->Organisation->getOrganisationTypeId() == 3){

            $this->view->dentistLabs = (count(LabDentists::find("dentist_id = ".$this->currentUser->getOrganisationId()." AND status = 'active'")) > 0) ? LabDentists::find("dentist_id = ".$this->currentUser->getOrganisationId()." AND status = 'active'") : NULL;
            $this->view->lastLocation = DentistOrder::findFirst('dentist_id = '.$order->getDentistId().' AND location_id IS NOT NULL AND order_at IS NOT NULL ORDER BY order_at DESC');
        }

        if($order->getDentistId() != NULL){

            $this->view->subDentists = Users::find('organisation_id = '.$order->getDentistId().' AND active = 1 AND role_template_id IN(4,10)');
            $this->view->locations = DentistLocation::find('dentist_id = '.$order->getDentistId());
        }

        $this->view->orderRecipes = $orderRecipes;
        $this->view->blockForm = ($order->getDentistId() == NULL && $order->getLabCreated() != NULL) ? true : false;
        $this->view->order = $order;
        $this->view->birthDate = $birthDate;
        $this->view->deliveryTime = $deliveryTime;
        $this->view->contactDetails = $contactDetails;
        $this->view->disableSubnav = true;
        $this->view->messages = DentistOrderNotes::find('order_id = ' . $order->getId());
        $this->view->files = $files;

    }

    public function detailsAction($code = NULL)
    {

        // If code is null, redirect
        if ($code == NULL) {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Order doesn\'t exist.')));
            $this->response->redirect('/dentist/order/');
            $this->view->disable();
            return;
        }

        $files = [];

        $field = ($this->currentUser->Organisation->getOrganisationTypeId() == 4) ? "lab_created" : "dentist_id";
        $order = DentistOrder::findFirst($field.' = '.$this->currentUser->getOrganisationId().' AND code = '.$code);
        $orderRecipes = DentistOrderRecipe::find('order_id = '.$order->getId().' AND deleted_at IS NULL AND deleted_by IS NULL');

        // If order does not exist
        if (!$order) {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Order doesn\'t exist.')));
            $this->response->redirect('/dentist/order/');
            $this->view->disable();
            return;
        }

        // If order is not in delivery or delivered cannot access details
        if (!in_array($order->getStatus(),[4,5])) {

            $this->response->redirect('/dentist/order/edit/'.$code);
            $this->view->disable();
            return;
        }
        $deliveryTime = [0];
        $contactDetails = [];

        foreach ($order->DentistOrderRecipe as $recipe) {

            $name = $recipe->Recipes->Lab->getName();
            $phone = $recipe->Recipes->Lab->getTelephone();
            $cd = [
                "name" => $name,
                "phone" => $phone
            ];
            if (!in_array($cd, $contactDetails)) {
                $contactDetails[] = $cd;
            }

            $dt = $recipe->Recipes->getDeliveryTime();

            if ($dt === null) {
                $deliveryTime[] = 0;
            }
            else {
                $deliveryTime[] = $dt;
            }
        }

        $deliveryTime = max($deliveryTime);

        // Save form data
//        if ($this->request->isPost()) {
//
//            $post = $this->request->getPost();
//            $postData = $this->request->getPost('data');
//            $orderNoteId = null;
//
//            $order->DentistOrderData->setPatientInitials($postData['patient_initials']);
//            $order->DentistOrderData->setPatientInsertion($postData['patient_insertion']);
//            $order->DentistOrderData->setPatientLastname($postData['patient_lastname']);
//            $order->DentistOrderData->setPatientGender($postData['patient_gender']);
//            $order->DentistOrderData->setPatientNumber($postData['patient_number']);
//
//            if($postData['patient_birth']['year'] != "-" && $postData['patient_birth']['month'] != "-" && $postData['patient_birth']['day'] != "-"){
//                $order->DentistOrderData->setPatientBirth($postData['patient_birth']['year']."-".$postData['patient_birth']['month']."-".$postData['patient_birth']['day']);
//            }
//            else {
//                $order->DentistOrderData->setPatientBirth(NULL);
//            }
//            $order->DentistOrderBsn->setBsn((!empty($this->request->getPost('bsn')) && $this->request->getPost('bsn') != 0) ? $this->request->getPost('bsn') : NULL);
//            $order->setDentistUserId((!empty($post['dentist_user_id'])) ? $post['dentist_user_id'] : NULL);
//            $order->setLocationId((!empty($post['location_id'])) ? $post['location_id'] : NULL);
//            $order->save();
//
//            if (!empty($this->request->getPost('description'))) {
//                $orderNotes = new DentistOrderNotes();
//                $orderNotes->setNote($this->request->getPost('description'));
//                $orderNotes->setOrderId($order->getId());
//                $orderNotes->save();
//                $orderNoteId = $orderNotes->getId();
//            }
//
//            if ($this->request->hasFiles() == true) {
//                $configDir = $this->config->application->dentistOrderDir;
//
//                // Print the real file names and their sizes
//                foreach ($this->request->getUploadedFiles() as $file) {
//                    $imgDir = $configDir;
//                    if (!is_dir($imgDir)) {
//                        mkdirR($imgDir);
//                    }
//
//                    // To avoid sending empty files, check if file has name
//                    if (!empty($file->getName())) {
//                        $file->moveTo($configDir . $file->getName());
//                        $orderFile = new DentistOrderFile();
//                        $orderFile->setOrderId($order->getId());
//                        $orderFile->setFileName($file->getName());
//                        $orderFile->setFilePath($configDir);
//                        $orderFile->setFileType($file->getType());
//                        if (!is_null($orderNoteId))
//                            $orderFile->setOrderNoteId($orderNoteId);
//                        $orderFile->save();
//                    }
//                }
//            }
//
//            if (isset($post['complete'])){
//                return $this->response->redirect('/dentist/order/complete/' . $code);
//            }
//            elseif($post['reload_after'] == 1){
//
//                if(isset($post['lab_choice']) && $post['lab_choice'] != ""){
//                    return $this->response->redirect('/dentist/order/add/'.$code.'/0/'.$post['lab_choice']);
//                }
//                else {
//                    return $this->response->redirect('/dentist/order/add/' . $code);
//                }
//            }
//            $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Order has been updated.')));
//        }

        $files = DentistOrderFile::find(array(
            'deleted_at IS NULL AND order_id = :orderid:',
            'bind' => array(
                'orderid' => $order->getId()
            ),
            'columns' => ['file_name', 'id']
        ))->toArray();

//        $order->delivery_at = date("Y-m-d", strtotime($order->delivery_at));

        if($order->DentistOrderData->patient_birth != NULL){
            $birthDate = [
                "day" => date("d", strtotime($order->DentistOrderData->patient_birth)),
                "month" => date("m", strtotime($order->DentistOrderData->patient_birth)),
                "year" => date("Y", strtotime($order->DentistOrderData->patient_birth))
            ];
        }

        // View vars and assets
        $this->assets->collection('footer')
            ->addJs("bower_components/moment/min/moment.min.js")
            ->addJs("js/app/orderFile.js");

        // If lab is ordering, get available dentists, or viceversa
        if($this->currentUser->Organisation->getOrganisationTypeId() == 4){

            $this->view->labDentists = LabDentists::find('lab_id = '.$this->currentUser->getOrganisationId());
            $this->view->lastLocation = DentistOrder::findFirst('lab_created = '.$order->getLabCreated().' AND location_id IS NOT NULL AND order_at IS NOT NULL ORDER BY order_at DESC');
        }
        elseif($this->currentUser->Organisation->getOrganisationTypeId() == 3){

            $this->view->dentistLabs = (count(LabDentists::find("dentist_id = ".$this->currentUser->getOrganisationId()." AND status = 'active'")) > 0) ? LabDentists::find("dentist_id = ".$this->currentUser->getOrganisationId()." AND status = 'active'") : NULL;
            $this->view->lastLocation = DentistOrder::findFirst('dentist_id = '.$order->getDentistId().' AND location_id IS NOT NULL AND order_at IS NOT NULL ORDER BY order_at DESC');
        }

        if($order->getDentistId() != NULL){

            $this->view->subDentists = Users::find('organisation_id = '.$order->getDentistId().' AND active = 1 AND role_template_id IN(4,10)');
            $this->view->locations = DentistLocation::find('dentist_id = '.$order->getDentistId());
        }

        $this->view->orderRecipes = $orderRecipes;
        $this->view->blockForm = ($order->getDentistId() == NULL && $order->getLabCreated() != NULL) ? true : false;
        $this->view->order = $order;
        $this->view->birthDate = $birthDate;
        $this->view->deliveryTime = $deliveryTime;
        $this->view->contactDetails = $contactDetails;
        $this->view->disableSubnav = true;
        $this->view->messages = DentistOrderNotes::find('order_id = ' . $order->getId());
        $this->view->files = $files;

    }

    public function ajaxfileremoveAction(){

        try {
            $post = $this->request->getPost();
            $orderFile = DentistOrderFile::findFirst('deleted_at IS NULL AND id = '.$post['fileId']);

            if ($orderFile) {

                $orderFile->softDelete();
                $this->session->set('message', array('type' => 'success', 'content' => Trans::make('File has been deleted.')));
            }
            else {
                $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('File doesn\'t exist.')));
            }
            return json_encode(["deleted" => $post['fileId']]);
        }
        catch (\Exception $e) {
            return json_encode(["Error" => $e->getMessage()]);
        }
    }

    public function ajaxFileAddAction($fileId){

    }

    public function ajaxeditdentistAction(){

        $this->view->disable();

        if ($this->request->isAjax()) {

            if(!empty($this->request->getPost('dentist_id'))){

                $order = DentistOrder::findFirst('id = '.$this->request->getPost('order_id'));
                $order->setDentistId($this->request->getPost('dentist_id'));
                $order->save();

                if($order->save()){
                    $result = [
                        "msg"   => Trans::make("Order updated"),
                        "status"    => "success"
                    ];
                }
                else {
                    $result = [
                        "msg"   => Trans::make("Error while updating order"),
                        "status"    => "error"
                    ];
                }
                return json_encode($result);
            }
        }
    }

    public function viewAction($code){

        $order = DentistOrder::findFirst('deleted_at IS NULL AND dentist_id = '.$this->currentUser->getOrganisationId().' AND code = '.$code);
        $orderRecipes = DentistOrderRecipe::find('order_id = '.$order->getId().' AND deleted_at IS NULL AND price IS NOT NULL');

        if (!$order) {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Order doesn\'t exist.')));
            $this->response->redirect('/dentist/order/');
            $this->view->disable();
            return;
        }
        $attachments = DentistOrderFile::find('order_id = '.$order->getId());

        if ($this->request->isAjax() && $this->request->isPost()) {

            $this->view->disable();
            $post = $this->request->getPost();
            $recipe = DentistOrderRecipe::findFirst($post['id']);
            $recipe->setStatus($post['newStatus']);
            $recipe->setStatusChangedBy($this->currentUser->getId());
            $recipe->setStatusChangedAt(Date::currentDatetime());
            $recipe->setStatusPrev($post['orgStatus']);

            if($recipe->save()){
                return json_encode($recipe);
            }
            else {
                return json_encode('error');
            }
            die;
        }

        if ($this->request->isPost()) {

            // Check if message is in post
            $orderNoteId = null;
            $description = $this->request->getPost('new_message');

            if (isset($description)) {

                // Save message to order notes
                if (!empty($description)) {
                    $orderNotes = new DentistOrderNotes();
                    $orderNotes->setNote($description);
                    $orderNotes->setOrderId($order->getId());
                    $orderNotes->save();
                    $orderNoteId = $orderNotes->getId();
                }
            }

            if ($this->request->hasFiles() == true) {

                $configDir = $this->config->application->dentistOrderDir;

                // Print the real file names and their sizes
                foreach ($this->request->getUploadedFiles() as $file) {

                    $imgDir = $configDir;

                    if (!is_dir($imgDir)) {
                        mkdirR($imgDir);
                    }
                    // To avoid sending empty files, check if file has name
                    if (!empty($file->getName())) {

                        $file->moveTo($configDir . $file->getName());
                        $orderFile = new DentistOrderFile();
                        $orderFile->setOrderId($order->getId());
                        $orderFile->setFileName($file->getName());
                        $orderFile->setFilePath($configDir);
                        $orderFile->setFileType($file->getType());

                        if ($orderNoteId != null) {
                            $orderFile->setOrderNoteId($orderNoteId);
                        }
                        $orderFile->save();
                    }
                }
            }
            $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Message has been added.')));
            $this->response->redirect('/dentist/order/view/' . $code);
        }
        $this->view->statuses = RecipeStatus::find()->toArray();
        $this->view->messages = DentistOrderNotes::find('order_id = ' . $order->getId());
        $this->view->order = $order;
        $this->view->orderRecipes = $orderRecipes;
        $this->view->attachments = $attachments;
        $this->view->disableSubnav = true;
    }

    public function calculatepriceAction($id){

        // Post vars?
        $postOptions = [];
        parse_str($this->request->getPost('options'), $postOptions);
        $customFieldPost = $postOptions['customField'];

        // Edit view?
        if ($this->request->hasPost('editview')) {

            $customFieldPost = $this->rebuildIdsForCalculation($customFieldPost);
        }

        // Find recipes
        $recipe = Recipes::findFirst('deleted_at IS NULL AND lab_id IS NOT NULL AND active = 1 AND code = '.$id);

        // Find discounts
        $dgd = DentistGroupDiscount::findFirst('code = '.$id);
        $price = $recipe->getPrice();

        // Find Signadens recipe
        $parentRecipe = $recipe->ParentRecipe;

        // Find tariff codes assigned to the organisation connected to the recipe
        $tariffs = CodeTariff::find('active = 1 AND organisation_id = '.$recipe->getOrganisationId());

        // Map Signa tariff codes
        $mappedSignaTariffs = [];

        foreach ($tariffs as $t) {

            $map = MapLabTariffLedger::findFirstByTariffId($t->getId());

            // If found then map
            if ($map) {
                $mappedSignaTariffs[$map->getSignaTariffId()] = $t;
            }
            else {
                if($t->getPrice() != NULL){
                    $mappedSignaTariffs[$t->getId()] = $t->getPrice();
                }
                else {
                    if($t->getMarginValue() != NULL){
                        $mappedSignaTariffs[$t->getId()] = $t->getMarginValue();
                    }
                    else {
                        $mappedSignaTariffs[$t->getId()] = $t->getPrice();
                    }
                }
            }
        }

        // Calculate price for custom fields
        foreach ($parentRecipe->RecipeCustomField as $customField) {

            if ($recipe->getPriceType() == 'Fixed' && $customField->getCustomFieldType() != 'optional') {
                continue;
            }

            if (
                $customField->getType() == 'number'
                && $customField->getCustomPriceTariffId()
                && $customField->getCustomPriceTariffId() != 0
                && isset($customFieldPost[$customField->getId()])
                && $customFieldPost[$customField->getId()]
            ) {
                if (isset($mappedSignaTariffs[$customField->getCustomPriceTariffId()])){

                    if ($customField->getCustomPriceType() == 1) {

                        $price += $mappedSignaTariffs[$customField->getCustomPriceTariffId()]->getPrice() * $customField->getAmount();
                    }
                    elseif ($customField->getCustomPriceType() == 2) {

                        $price += ($mappedSignaTariffs[$customField->getCustomPriceTariffId()]->getPrice() * $customFieldPost[$customField->getId()] * $customField->getAmount());
                    }
                }
            }
            elseif ($customField->getType() == 'checkbox') {

                /* @var $option RecipeCustomFieldOptions */
                /* @var RecipeCustomFieldOptions[] $customField ->Options List of RecipeCustomFieldOptions objects. */
                foreach ($customField->Options as $option) {

                    if (isset($customFieldPost[$customField->getId()]) && isset($customFieldPost[$customField->getId()][$option->getId()])) {

                        if ($option->getCustomPriceTariffId() != null && $option->getCustomPriceTariffId() != 0 && $option->getTariffId() == 0) {

                            $price += $option->getCustomPriceTariffId() * $customField->getAmount();
                        }
                        elseif ($option->getTariffId() != 0 && $option->Tariff && isset($mappedSignaTariffs[$option->getTariffId()])) {

                            // we have to get lab tariff connected to signa tariff
                            $price += $mappedSignaTariffs[$option->getTariffId()]->getPrice() * $customField->getAmount();
                        }
                    }
                }
            }
            elseif ($customField->getType() == 'select') {

                /* @var $option RecipeCustomFieldOptions */
                /* @var RecipeCustomFieldOptions[] $customField ->Options List of RecipeCustomFieldOptions objects. */
                foreach ($customField->Options as $option) {

                    if (isset($customFieldPost[$customField->getId()]) && $option->getValue() == $customFieldPost[$customField->getId()]) {

                        if ($option->getCustomPriceTariffId() != null && $option->getCustomPriceTariffId() != 0 && $option->getTariffId() == 0) {

                            $price += $option->getCustomPriceTariffId() * $customField->getAmount();
                        }
                        elseif ($option->getTariffId() != 0 && $option->Tariff && isset($mappedSignaTariffs[$option->getTariffId()])) {

                            // we have to get lab tariff connected to signa tariff
                            $price += $mappedSignaTariffs[$option->getTariffId()]->getPrice() * $customField->getAmount();
                        }
                    }
                }
            }
            elseif($customField->getType() == 'statement'){

                if (isset($mappedSignaTariffs[$customField->getCustomPriceTariffId()])) {

                    $price += $mappedSignaTariffs[$customField->getCustomPriceTariffId()]->getPrice() * $customField->getAmount();
                }
            }
        }

        // Recipe activity price
        foreach ($parentRecipe->RecipeActivity as $recipeActivity) {

            if ($recipe->getPriceType() == 'Fixed') {
                continue;
            }

            if ($recipeActivity->getTariffId() != 0 && $recipeActivity->Tariff && isset($mappedSignaTariffs[$recipeActivity->getTariffId()])) {
                $price += $mappedSignaTariffs[$recipeActivity->getTariffId()]->getPrice() * $recipeActivity->getAmount();
            }
            elseif ($recipeActivity->getTariffId() != 0 && $recipeActivity->Tariff && !isset($mappedSignaTariffs[$recipeActivity->getTariffId()])){
                $price += $recipeActivity->Tariff->getPrice() * $recipeActivity->getAmount();
            }
        }

        // If discounts found, apply
        if ($dgd != false) {
            $price = $dgd->getDiscountPrice($price);
        }
        return json_encode(number_format($price, 2, '.', ''));
    }

    public function recipedetailsAction($dentistRecipeId = NULL){

        // If recipe id is null, redirect
        if ($dentistRecipeId == NULL) {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make("Recipe doesn't exist.")));
            $this->response->redirect('/dentist/order/');
            $this->view->disable();
            return;
        }

        // Find recipe in order
        $recipe = DentistOrderRecipe::findFirst('id='.$dentistRecipeId);

        // Find tariff codes
        $tariffs = CodeTariff::find('active = 1 AND organisation_id = '.$recipe->Recipes->getOrganisationId());
        $mappedSignaTariffs = [];

        foreach ($tariffs as $tariff) {

            $map = MapLabTariffLedger::findFirstByTariffId($tariff->getId());

            if ($map) {
                $mappedSignaTariffs[$map->getSignaTariffId()] = $tariff;
            }
        }
        $myTariffs = array();

        foreach($recipe->Recipes->ParentRecipe->RecipeActivity as $recipeActivity){

            if ($recipeActivity->getTariffId() != 0 && $recipeActivity->Tariff && isset($mappedSignaTariffs[$recipeActivity->getTariffId()])) {
                $myTariffs[$recipeActivity->getTariffId()]['code'] = $mappedSignaTariffs[$recipeActivity->getTariffId()]->getCode();
                $myTariffs[$recipeActivity->getTariffId()]['description'] = $mappedSignaTariffs[$recipeActivity->getTariffId()]->getDescription();
                $myTariffs[$recipeActivity->getTariffId()]['price'] = $mappedSignaTariffs[$recipeActivity->getTariffId()]->getPrice();
            }
        }
        $schema_default = json_decode($recipe->getSchemaValues());

        if($schema_default == NULL){
            $schema_default = [];
        }

        $statusesTime = RecipeStatusTime::query()
            ->where('lab_id = :lab_id:')
            ->bind([
                'lab_id' => $recipe->Recipes->getOrganisationId(),
            ])
            ->execute();

        $statusesTimeArr = [];
        /** @var RecipeStatusTime $statusTime */
        foreach ($statusesTime as $statusTime) {
            $statusesTimeArr[$statusTime->getRecipeStatusId()] = $statusTime;
        }

        foreach($recipe->Recipes->ParentRecipe->RecipeCustomField as $cf){

            if($cf->getCustomFieldType() == 'variable'){
                $availableCustomFields['var'] = 1;
            }
            elseif($cf->getCustomFieldType() == 'optional'){
                $availableCustomFields['opt'] = 1;
            }
            elseif($cf->getCustomFieldType() == 'additional'){
                $availableCustomFields['add'] = 1;
            }
        }

        $recipeBase = Recipes::findFirstById($recipe->Recipes->getParentId());

        $this->view->availableCustomFields = $availableCustomFields;
        $this->assets->collection('additional')->addJs('bower_components/tinymce/tinymce.min.js');
        $this->view->statuses_times = $statusesTimeArr;
        $this->view->statuses_av = RecipeStatus::find();
        $this->view->hasActivity = 0;
        $this->view->schema_default = $schema_default;
        $this->view->recipe = $recipe;
        $this->view->recipeBase = $recipeBase;
        $this->view->myTariffs  = $myTariffs;
    }

    public function editproductAction($dentistRecipeId){

        /** @var DentistOrderRecipe $recipe */
        $recipe = DentistOrderRecipe::findFirst('id='.$dentistRecipeId);

        /* @var CodeTariff[] $tariffs */
        $tariffs = CodeTariff::find('active = 1 AND organisation_id = '.$recipe->Recipes->getOrganisationId());

        /* @var $map MapLabTariffLedger */
        /* @var CodeTariff[] $mappedSignaTariffs */
        $mappedSignaTariffs = [];

        foreach ($tariffs as $tariff) {

            $map = MapLabTariffLedger::findFirstByTariffId($tariff->getId());

            if ($map) {
                $mappedSignaTariffs[$map->getSignaTariffId()] = $tariff;
            }
        }
        $myTariffs = array();

        foreach($recipe->Recipes->ParentRecipe->RecipeActivity as $recipeActivity){

            if ($recipeActivity->getTariffId() != 0 && $recipeActivity->Tariff && isset($mappedSignaTariffs[$recipeActivity->getTariffId()])) {

                $myTariffs[$recipeActivity->getTariffId()]['code'] = $mappedSignaTariffs[$recipeActivity->getTariffId()]->getCode();
                $myTariffs[$recipeActivity->getTariffId()]['description'] = $mappedSignaTariffs[$recipeActivity->getTariffId()]->getDescription();
                $myTariffs[$recipeActivity->getTariffId()]['price'] = $mappedSignaTariffs[$recipeActivity->getTariffId()]->getPrice();
            }
        }

        if ($this->request->isPost()) {

            $recipe->setPrice($this->request->getPost('price'));

            if($this->request->hasPost('teeth')){

                $teethNum = array();

                foreach($this->request->getPost('teeth') as $k => $v){

                    if($v != 0){
                        $teethNum[] = $k;
                    }
                }

                if(!empty($teethNum)){

                    $schema_values = json_encode($teethNum);
                    $recipe->setSchemaValues($schema_values);
                }
            }
            $recipe->save();

            if($this->request->hasPost('deletedFile')){

                foreach($this->request->getPost('deletedFile') as $k => $v){

                    if($v != 0){
                        $file = DentistOrderFile::findFirst('id='.$k);
                        $file->setDeletedAt(date("Y-m-d H:i:s"));
                        $file->setDeletedBy($this->currentUser->getOrganisationId());
                        $file->save();
                    }
                }
            }

            if($this->request->hasPost('delivery_old')){

                foreach ($this->request->getPost('delivery_old') as $delivery){

                    $oldDelivery = DentistOrderRecipeDelivery::findFirst('id = '.$delivery['id']);
                    $oldDelivery->setDeliveryDate(date("Y-m-d", strtotime($delivery['date'])));
                    $oldDelivery->setDeliveryText($delivery['text']);
                    $oldDelivery->setDays($delivery['days']);
                    $oldDelivery->setPartOfDay($delivery['part_of_day']);
                    $oldDelivery->setRecipeStatusId($delivery['phase']);
                    $oldDelivery->save();
                }
            }

            if($this->request->hasPost('delivery_new')){

                foreach ($this->request->getPost('delivery_new') as $delivery){

                    $newDelivery = new DentistOrderRecipeDelivery();
                    $newDelivery->setDentistOrderId($recipe->DentistOrder->getId());
                    $newDelivery->setDentistOrderRecipeId($dentistRecipeId);
                    $newDelivery->setDeliveryDate(date("Y-m-d", strtotime($delivery['date'])));
                    $newDelivery->setDeliveryText($delivery['text']);
                    $newDelivery->setDays($delivery['days']);
                    $newDelivery->setPartOfDay($delivery['part_of_day']);
                    $newDelivery->setRecipeStatusId($delivery['phase']);
                    $newDelivery->save();
                }
            }
            $orderNoteId = null;

            if (!empty($this->request->getPost('description'))) {

                $orderNotes = new DentistOrderNotes();
                $orderNotes->setNote($this->request->getPost('description'));
                $orderNotes->setOrderId($recipe->DentistOrder->getId());
                $orderNotes->save();
                $orderNoteId = $orderNotes->getId();
            }

            try {
                if ($this->request->hasFiles() == true) {

                    $configDir = $this->config->application->dentistOrderDir;

                    // Print the real file names and their sizes
                    foreach ($this->request->getUploadedFiles() as $file) {

                        $imgDir = $configDir;

                        if (!is_dir($imgDir)) {
                            mkdirR($imgDir);
                        }

                        // To avoid sending empty files, check if file has name
                        if (!empty($file->getName())) {

                            $file->moveTo($configDir . $file->getName());
                            $orderFile = new DentistOrderFile();
                            $orderFile->setOrderId($recipe->DentistOrder->getId());
                            $orderFile->setOrderRecipeId($recipe->getId());
                            $orderFile->setFileName($file->getName());
                            $orderFile->setFilePath($configDir);
                            $orderFile->setFileType($file->getType());

                            if ($orderNoteId != null) {
                                $orderFile->setOrderNoteId($orderNoteId);
                            }
                            $orderFile->save();
                        }
                    }
                }
            }
            catch (\Exception $e) {
                var_dump($e->getMessage());
            }

            if (!empty($this->request->getPost('customField'))) {

                if (in_array($recipe->DentistOrder->status, [0,4,5])) {

                    $this->session->set('message', [['type' => 'warning', 'content' => Trans::make('You cant save custom options for current order status')]]);
                }
                else {
                    $customFields = $this->request->getPost('customField');
                    $customFieldsLab = [];

                    if ($this->request->hasPost('customFieldLab')) {

                        $customFieldsLab = $this->request->getPost('customFieldLab');
                    }

                    foreach ($customFields as $customFieldId => $customField) {

                        /** @var DentistOrderRecipeData $orderRecipeData */
                        $orderRecipeData = DentistOrderRecipeData::findFirstById($customFieldId);

                        if (!$orderRecipeData) {
                            continue;
                        }
                        $customFieldProcessed = [];

                        if (is_array($customField)) {

                            foreach($customField as $k => $v){

                                if($v == NULL){
                                    $customFieldProcessed[$k] = "none";
                                }
                                else {
                                    $customFieldProcessed[$k] = $v;
                                }
                            }
                            $orderRecipeData->setFieldValue(json_encode($customFieldProcessed));
                            $orderRecipeData->setFieldDentistValue(json_encode($customFieldProcessed));
                        }
                        else {
                            $orderRecipeData->setFieldValue($customField);
                            $orderRecipeData->setFieldDentistValue($customField);
                        }

                        if (isset($customFieldsLab[$customFieldId])) {
                            $orderRecipeData->setHasLabCheck(1);
                        }

                        $saved = $orderRecipeData->save();

                        // @TODO: tarif codes here;
                        // what if many tariffs??
                    }
                }
            }

            $currMessages = (array)$this->session->get('message');
            $currMessages[] = ['type' => 'success', 'content' => Trans::make('Recipe modified')];

            $this->session->set('message', $currMessages);
            $this->response->redirect('/dentist/order/edit/'.$recipe->DentistOrder->code);
            $this->view->disable();
            return;
        }
        $schema_default = json_decode($recipe->getSchemaValues());

        if($schema_default == NULL){
            $schema_default = [];
        }

        $statusesTime = RecipeStatusTime::query()
            ->where('lab_id = :lab_id:')
            ->bind([
                'lab_id' => $recipe->Recipes->getOrganisationId(),
            ])
            ->execute();

        $statusesTimeArr = [];
        /** @var RecipeStatusTime $statusTime */
        foreach ($statusesTime as $statusTime) {

            $statusesTimeArr[$statusTime->getRecipeStatusId()] = $statusTime;
        }

        foreach($recipe->Recipes->ParentRecipe->RecipeCustomField as $cf){

            if($cf->getCustomFieldType() == 'variable'){
                $availableCustomFields['var'] = 1;
            }
            elseif($cf->getCustomFieldType() == 'optional'){
                $availableCustomFields['opt'] = 1;
            }
            elseif($cf->getCustomFieldType() == 'additional'){
                $availableCustomFields['add'] = 1;
            }
        }
        $recipeBase = Recipes::findFirstById($recipe->Recipes->getParentId());

        $this->view->availableCustomFields = $availableCustomFields;
        $this->assets->collection('additional')->addJs('bower_components/tinymce/tinymce.min.js');
        $this->view->statuses_times = $statusesTimeArr;
        $this->view->statuses_av = RecipeStatus::find();
        $this->view->hasActivity = 0;
        $this->view->schema_default = $schema_default;
        $this->view->recipe = $recipe;
        $this->view->recipeBase = $recipeBase;
        $this->view->myTariffs  = $myTariffs;
    }

    public function showproductAction($code, $id){

        if($this->currentUser->Organisation->getOrganisationTypeId() == 4){

            $order = DentistOrder::findFirst('deleted_at IS NULL AND lab_created = '.$this->currentUser->Organisation->getId().' AND code = '.$code);
        }
        else {
            $order = DentistOrder::findFirst('deleted_at IS NULL AND dentist_id = '.$this->currentUser->Organisation->getId().' AND code = '.$code);
        }

        if (!$order) {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Order doesn\'t exist.')));
            $this->response->redirect('/dentist/order/');
            $this->view->disable();
            return;
        }

        /** @var Recipes $recipe */
        $recipe = Recipes::findFirst('deleted_at IS NULL AND lab_id IS NOT NULL AND active = 1 AND code = '.$id);

        if (!$recipe) {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Recipe doesn\'t exist.')));
            $this->response->redirect('/dentist/order/');
            $this->view->disable();
            return;
        }
        $dgd = DentistGroupDiscount::findFirst('code = '.$id);



        /* @var CodeTariff[] $tariffs */
        $signaTariffs = CodeTariff::find('active = 1 AND organisation_id = '.$recipe->ParentRecipe->getOrganisationId())->toArray();
        $labTariffs = CodeTariff::find('active = 1 AND organisation_id = '.$recipe->getOrganisationId())->toArray();

        /* @var CodeTariff[] $mappedSignaTariffs */
        $mappedSignaTariffs = [];

        foreach ($signaTariffs as $t) {

//            $map = CodeTariff::find('active = 1 AND organisation_id = '.$recipe->getOrganisationId().' AND signa_tariff_id = '.$t->getId());
//
//            if ($map) {
//                $mappedSignaTariffs[$map->signa_tariff_id] = $t;
//                _dump($mappedSignaTariffs);exit(1);
//            }

            foreach ($labTariffs as $lt){

                if($lt['signa_tariff_id'] == $t['id']){

                    $mappedSignaTariffs[$t['id']] = $lt;
                }
            }
        }


//        $myTariffs = array();
////
////        foreach($recipe->ParentRecipe->RecipeActivity as $recipeActivity){
////
////            if ($recipeActivity->getTariffId() != 0 && $recipeActivity->Tariff && isset($mappedSignaTariffs[$recipeActivity->getTariffId()])) {
////
////                $myTariffs[$recipeActivity->getTariffId()]['code'] = $mappedSignaTariffs[$recipeActivity->getTariffId()]->getCode();
////                $myTariffs[$recipeActivity->getTariffId()]['description'] = $mappedSignaTariffs[$recipeActivity->getTariffId()]->getDescription();
////                $myTariffs[$recipeActivity->getTariffId()]['price'] = $mappedSignaTariffs[$recipeActivity->getTariffId()]->getPrice();
////            }
////        }

//        _dump($mappedSignaTariffs);exit();

        if (!$recipe) {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Product doesn\'t exist.')));
            $this->response->redirect('/dentist/order/');
            $this->view->disable();
            return;
        }

        if ($dgd != false) {
            $recipe->setPrice($dgd->getDiscountPrice());
        }

        // Post
        if ($this->request->isPost()) {

            if (!$this->security->checkToken()) {
                return false;
            }

            $availableStatuses = json_decode($recipe->getStatuses());
            $firstStatus = array_shift($availableStatuses);

            $orderRecipe = new DentistOrderRecipe();
            $orderRecipe->setOrderId($order->getId());
            $orderRecipe->setRecipeId($recipe->getId());
            $orderRecipe->setStatus($firstStatus->id);
            $orderRecipe->setPrice($this->request->getPost('price'));

            if($this->request->hasPost('teeth')){

                $teethNum = array();

                foreach($this->request->getPost('teeth') as $k => $v){

                    if($v != 0){
                        $teethNum[] = $k;
                    }
                }

                if(!empty($teethNum)){

                    $schema_values = json_encode($teethNum);
                    $orderRecipe->setSchemaValues($schema_values);
                }
            }
            $orderRecipe->save();

            if($this->request->hasPost('delivery')){

                foreach ($this->request->getPost('delivery') as $delivery){

                    $newDelivery = new DentistOrderRecipeDelivery();
                    $newDelivery->setDentistOrderId($order->getId());
                    $newDelivery->setDentistOrderRecipeId($orderRecipe->getId());
                    $newDelivery->setDeliveryDate(date("Y-m-d", strtotime($delivery['date'])));
                    $newDelivery->setDeliveryText($delivery['text']);
                    $newDelivery->setDays($delivery['days']);
                    $newDelivery->setPartOfDay($delivery['part_of_day']);
                    $newDelivery->setRecipeStatusId($delivery['phase']);
                    $newDelivery->save();
                }
            }

            try {
                if ($this->request->hasFiles() == true) {

                    $configDir = $this->config->application->dentistOrderDir;

                    // Print the real file names and their sizes
                    foreach ($this->request->getUploadedFiles() as $file) {

                        $imgDir = $configDir;
                        if (!is_dir($imgDir)) {
                            mkdirR($imgDir);
                        }

                        // To avoid sending empty files, check if file has name
                        if (!empty($file->getName())) {

                            $file->moveTo($configDir . $file->getName());
                            $orderFile = new DentistOrderFile();
                            $orderFile->setOrderId($order->getId());
                            $orderFile->setOrderRecipeId($orderRecipe->getId());
                            $orderFile->setFileName($file->getName());
                            $orderFile->setFilePath($configDir);
                            $orderFile->setFileType($file->getType());
                            $orderFile->save();
                        }
                    }
                }
            }
            catch (\Exception $e) {
                var_dump($e->getMessage());
            }

            if (!empty($this->request->getPost('customField'))) {

                $customFields = $this->request->getPost('customField');
                $customFieldsLab = [];

                if ($this->request->hasPost('customFieldLab')) {

                    $customFieldsLab = $this->request->getPost('customFieldLab');
                }

                foreach ($customFields as $customFieldId => $customField) {

                    $customFieldModel = RecipeCustomField::findFirst($customFieldId);

                    $orderRecipeData = new DentistOrderRecipeData();
                    $orderRecipeData->setOrderRecipeId($orderRecipe->getId());
                    $orderRecipeData->setRecipeCustomFieldId($customFieldModel->getId());
                    $orderRecipeData->setFieldName($customFieldModel->getName());
                    $orderRecipeData->setFieldType($customFieldModel->getType());
                    $orderRecipeData->setAmount($customFieldModel->getAmount());

                    if (isset($mappedSignaTariffs[$customFieldModel->getCustomPriceTariffId()])) {

                        $orderRecipeData->setCustomPriceTariffId($mappedSignaTariffs[$customFieldModel->getCustomPriceTariffId()]->getId());
                    }
                    $orderRecipeData->setCustomPriceType($customFieldModel->getCustomPriceType());

                    if (is_array($customField)) {

                        foreach($customField as $k => $v){

                            if($v == NULL){
                                $customFieldProcessed[$k] = "none";
                            }
                            else {
                                $customFieldProcessed[$k] = $v;
                            }
                        }
                        $orderRecipeData->setFieldValue(json_encode($customFieldProcessed));
                        $orderRecipeData->setFieldDentistValue(json_encode($customFieldProcessed));
                    }
                    else {
                        $orderRecipeData->setFieldValue($customField);
                        $orderRecipeData->setFieldDentistValue($customField);
                    }

                    if (isset($customFieldsLab[$customFieldId])) {

                        $orderRecipeData->setHasLabCheck(1);
                    }
                    $saved = $orderRecipeData->save();

                    if (!$saved) {
                        continue;
                    }

                    if (!$customFieldModel->Options) {
                        continue;
                    }

                    // @var RecipeCustomFieldOptions $option
                    foreach ($customFieldModel->Options as $option) {

                        $orderFieldOption = new DentistOrderRecipeDataOptions();
                        $orderFieldOption->setDentistOrderRecipeDataId($orderRecipeData->getId());
                        $orderFieldOption->setCustomPriceTariffId($option->getCustomPriceTariffId());
                        $orderFieldOption->setOption($option->getOption());

                        if ($option->getTariffId() && isset($mappedSignaTariffs[$option->getTariffId()])) {

                            $orderFieldOption->setTariffId($mappedSignaTariffs[$option->getTariffId()]->getId());
                        }
                        $orderFieldOption->setValue($option->getValue());
                        $orderFieldOption->save();
                    }
                }
            }
            $order->setStatus(1);
            $order->save();

            $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Product has been added.')));
            $this->response->redirect('/dentist/order/edit/' . $order->getCode());
            $this->view->disable();
            return;
        }

        $availableCustomFields = [
            'var' => 0,
            'opt' => 0,
            'add' => 0
        ];

        foreach($recipe->ParentRecipe->RecipeCustomField as $cf){

            if($cf->getCustomFieldType() == 'variable'){
                $availableCustomFields['var'] = 1;
            }
            elseif($cf->getCustomFieldType() == 'optional'){
                $availableCustomFields['opt'] = 1;
            }
            elseif($cf->getCustomFieldType() == 'additional'){
                $availableCustomFields['add'] = 1;
            }
        }

        $this->assets->collection('additional')->addJs('bower_components/tinymce/tinymce.min.js');

        $this->view->availableCustomFields = $availableCustomFields;
        $this->view->hasActivity = 0;
        $this->view->order = $order;
        $this->view->recipe = $recipe;
        $this->view->recipeBase = $recipe->ParentRecipe;
        $this->view->tariffs    = $tariffs;
        $this->view->myTariffs  = $mappedSignaTariffs;
        $this->view->disableSubnav = true;

        $statusesTime = RecipeStatusTime::query()
            ->where('lab_id = :lab_id:')
            ->bind([
                'lab_id' => $recipe->getOrganisationId(),
            ])
            ->execute();

        $statusesTimeArr = [];

        /** @var RecipeStatusTime $statusTime */
        foreach ($statusesTime as $statusTime) {

            $statusesTimeArr[$statusTime->getRecipeStatusId()] = $statusTime;
        }
        $this->view->statuses_times = $statusesTimeArr;
        $this->view->statuses_av = RecipeStatus::find();

        $this->view->setVars([
            'tokenKey' => $this->security->getTokenKey(),
            'token' => $this->security->getToken()
        ]);
    }

    private function findPossibleCategoriesAndRecipes($labDentist){

        $result = array(
            'categories' => array(0),
            'recipes' => array(0)
        );
        error_reporting(E_ALL);
        ini_set('display_errors', 'On');

        foreach ($labDentist as $lab) {

            $recipesQr = Recipes::query()
                ->where('deleted_at IS NULL AND active = :active: AND lab_id = :labid:')
                ->bind([
                    'active' => 1,
                    'labid' => $lab['lab_id']
                ]);

            $recipes = $recipesQr->execute();

            if (count($recipes) > 0) {

                foreach ($recipes as $recipe) {

                    $result['recipes'][] = $recipe->getId();
                    $result['categories'] = array_merge($result['categories'], RecipesLib::getCategoriesIdsArr($recipe->ParentRecipe));
                }
            }
        }
        return $result;
    }

    public function addAction($code = null, $id = '0', $labId = NULL){

        $noCategories = false;
        $recipeSettings = RecipeDefaultSetting::find();
        $currentPage = isset($_GET["page"]) && $_GET["page"] != null ? (int)$_GET["page"] : 1;
        $result = CategoryTree::find(["parent_id = '$id'"]);
        $breadcrumbs = $this->generateCatTreeBreadCrumbs($id);

        $orderLab = DentistOrder::findFirst('code = '.$code);

        // If dentist is connected to multiple labs and lab is selected then choose lab, else select all labs connected
        if($labId != NULL){
            $labDentist[] = LabDentists::findFirst('dentist_id = '.$this->currentUser->getOrganisationId().' AND lab_id = '.$labId)->toArray();
        }
        else {
            $labDentist = LabDentists::find(array(
                'dentist_id = :dentist:',
                'bind' => array(
                    'dentist' => ($this->currentUser->Organisation->getOrganisationTypeId() == 4) ? $orderLab->getDentistId() : $this->currentUser->getOrganisationId()
                ),
            ))->toArray();
        }

        if(($labDentist[0]['active_recipes'] == NULL)){
            $noCategories = true;
        }

        $posibleArr = $this->findPossibleCategoriesAndRecipes($labDentist);
        $hasActiveRecipesIds = [];

        foreach ($result as $key => $category) {

            $thisId = $category->getId();

            if (!in_array($thisId, $posibleArr['categories'])) {
                continue;
            }

            $childs = CategoryTree::find(
                [
                    "parent_id = '$thisId'",
                    "order" => "sort"
                ]
            );

            if ($this->checkChild($childs, $thisId, true, $posibleArr) == 1) {
                $hasActiveRecipesIds[] = $thisId;
            }
        }
        $currentCategory = CategoryTree::findFirst($id);


        if ($currentCategory && $noCategories == false) {

            $categoryRecipesQ = Recipes::query()
                ->join('Signa\Models\Recipes', 'parent.id = Signa\Models\Recipes.parent_id', 'parent')
                ->join('Signa\Models\CategoryTreeRecipes','ctr.category_tree_id = :ctr_id: AND parent.id = ctr.recipe_id','ctr')
                ->where('Signa\Models\Recipes.deleted_at IS NULL')
                ->andWhere('Signa\Models\Recipes.active = 1')
                ->andWhere('Signa\Models\Recipes.id IN ({possibleRecipes:array})')
                ->groupBy('Signa\Models\Recipes.id');

            $categoryRecipesBind = [
                'ctr_id' => $currentCategory->getId(),
//                'possibleRecipes' => ($labId != NULL) ? json_decode($labDentist[0]['active_recipes'], true) : $posibleArr['recipes']
                'possibleRecipes' => json_decode($labDentist[0]['active_recipes'], true)
            ];
            $basicRecipes = $categoryRecipesQ->bind($categoryRecipesBind)->execute();

            $settingsRequest = $this->request->getQuery('setting', []);

            if (!empty($settingsRequest)) {

                foreach ($settingsRequest as $settingId => $optionId) {

                    $categoryRecipesQ->join(
                        'Signa\Models\RecipeSetting',
                        'parent.id = setting'.$settingId.'.recipe_id',
                        'setting'.$settingId
                    );
                    $categoryRecipesQ->andWhere(
                        'setting'.$settingId.'.setting_id = '.$settingId.' AND setting'.$settingId.'.option_id = '.$optionId
                    );
                }
            }
            $categoryRecipes = $categoryRecipesQ->bind($categoryRecipesBind)->execute();
        }

        if (empty($hasActiveRecipesIds)) {

            $data = CategoryTree::find(
                [
                    'parent_id = :id:',
                    'bind' => [
                        'id' => $id
                    ],
                    "order" => "sort"
                ]
            );
            $noCategories = true;
        }
        else {
            $data = CategoryTree::find(
                [
                    'id IN ({id:array})',
                    'bind' => [
                        'id' => $hasActiveRecipesIds
                    ],
                    "order" => "sort"
                ]
            );
        }

        if (!$noCategories or count($currentCategory->Recipes) > 0) {

            $paginator = new PaginatorModel(
                [
                    "data" => $data,
                    "limit" => 9,
                    "page" => $currentPage,
                ]
            );
            $page = $paginator->getPaginate();

            if (count($labDentist) > 0) {

                $lab = Organisations::findFirst($labDentist[0]['lab_id']);
                $this->view->labLogo = $lab->getLogo();
            }

            $this->assets->collection('footer')
                ->addJs("bower_components/bootstrap3-typeahead/bootstrap3-typeahead.min.js")
                ->addJs("js/app/recipes.js");

            $this->view->categoryImage = '/uploads/images/category_tree/';
            $this->view->recipeImage = '/uploads/images/recipes/';
            $this->view->currentid = $id;
            $this->view->orderId = $code;
            $this->view->page = $page;
            $this->view->data = $data;
            $this->view->currentCategory = $currentCategory;
            $this->view->disableSubnav = true;
        }
        else {
            $this->view->noCategories = $noCategories;
        }

        $this->view->labId = $labId;
        $this->view->categoryRecipes = $categoryRecipes;
        $this->view->basicRecipe = $basicRecipes;
        $this->view->breadcrumbs = $breadcrumbs;
        $this->view->posibleArr = $posibleArr;
        $this->view->recipeSettings = $recipeSettings;
        $this->view->recipeSettingsSelected = $this->request->getQuery('setting', []);
    }

    public function historyAction(){

        $filters = null;
        $dentistUser = Users::findfirst("id = ".$this->currentUser->getId());

        /*if($dentistUser->getMainLocationId() != NULL){
            $orders = DentistOrder::find('deleted_at IS NULL AND status >= 4 AND dentist_id = '.$this->currentUser->getOrganisationId().' AND location_id = '.$dentistUser->getMainLocationId().' ORDER BY created_at DESC');
        }
        else {*/
            $orders = DentistOrder::find('deleted_at IS NULL AND status >= 4 AND dentist_id = '.$this->currentUser->getOrganisationId().' ORDER BY created_at DESC');
        //}

        // Save form data
        if ($this->request->isPost()) {

            // Post var allocation
            $location = $this->request->getPost('location');

            foreach ($location as $k => $v){

                if($v == 1){
                    $filters[] = $k;
                }
            }

            // Location filters
            if($filters != NULL){
                $orders = DentistOrder::find('deleted_at IS NULL AND status >= 4 AND dentist_id = '.$this->currentUser->getOrganisationId().' AND location_id IN('.implode(",",$filters).') ORDER BY created_at DESC');
            }
        }
        $this->view->dentistUser = $dentistUser;
        $this->view->filters = $filters;
        $this->view->orders = $orders;
        $this->view->locations = DentistLocation::find('dentist_id = '.$this->currentUser->getOrganisationId());
        $this->view->disableSubnav = true;
    }

    public function inprogressAction(){

        $filters = null;
        $dentistUser = Users::findfirst("id = ".$this->currentUser->getId());

        /*if($dentistUser->getMainLocationId() != NULL){
            $orders = DentistOrder::find('deleted_at IS NULL AND status IN (2,3) AND dentist_id = '.$this->currentUser->getOrganisationId().' AND location_id = '.$dentistUser->getMainLocationId().' ORDER BY created_at DESC');
        }
        else {*/
            $orders = DentistOrder::find('deleted_at IS NULL AND status IN (2,3) AND dentist_id = '.$this->currentUser->getOrganisationId().' ORDER BY created_at DESC');
        //}

        // Save form data
        if ($this->request->isPost()) {

            // Post var allocation
            $location = $this->request->getPost('location');

            foreach ($location as $k => $v){

                if($v == 1){
                    $filters[] = $k;
                }
            }

            // Location filters
            if($filters != NULL){
                $orders = DentistOrder::find('deleted_at IS NULL AND status > 1 AND dentist_id = '.$this->currentUser->getOrganisationId().' AND location_id IN('.implode(",",$filters).') ORDER BY created_at DESC');
            }
        }
        $this->view->dentistUser = $dentistUser;
        $this->view->filters = $filters;
        $this->view->orders = $orders;
        $this->view->locations = DentistLocation::find('dentist_id = '.$this->currentUser->getOrganisationId());
        $this->view->disableSubnav = true;
    }

    public function downloadAction($id){

        $dentistOrderFile = DentistOrderFile::findFirst($id);
        $fileName = $dentistOrderFile->getFileName();
        $filePath = 'https://'.$_SERVER['SERVER_NAME'].'/public/uploads/attachments/dentist_order/';
        $file = $filePath . rawurlencode($fileName);

        $headers = (stripos(get_headers($file)[0], "200 OK")) ? true : false;

        if ($headers == true) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;

        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Attachment doesn\'t exist.')));
            $this->response->redirect('/dentist/order/');
        }
    }

    public function searchAction($orderId)
    {
        $this->view->disableSubnav = true;
        $currentPage = isset($_GET["page"]) && $_GET["page"] != null ? (int)$_GET["page"] : 1;
        $searchString = isset($_GET["query"]) && $_GET["query"] != null ? $_GET["query"] : '';

        $orderLab = DentistOrder::findFirst('code = '.$orderId);

        $labDentist = LabDentists::find(array(
            'dentist_id = :dentist:',
            'bind' => array(
                'dentist' => ($this->currentUser->Organisation->getOrganisationTypeId() == 4) ? $orderLab->getDentistId() : $this->currentUser->getOrganisationId()
            ),
            'columns' => 'lab_id'
        ))->toArray();

        $posibleArr = $this->findPossibleCategoriesAndRecipes($labDentist);

        if($searchString != '' && $searchString != NULL){
            $recipes = Recipes::find('deleted_at IS NULL AND active = 1 AND id ='.$searchString);
        }
        else {
            $this->session->set('message', array('type' => 'error', 'content' => Trans::make('No recipes found.')));
            $this->response->redirect('/dentist/order/add/'.$orderId);
            $this->view->disable();
            return;
        }

        $paginator = new PaginatorModel(
            [
                "data" => $recipes,
                "limit" => 6,
                "page" => $currentPage,
            ]
        );
        $page = $paginator->getPaginate();

        $this->assets->collection('footer')
            ->addJs("bower_components/bootstrap3-typeahead/bootstrap3-typeahead.min.js")
            ->addJs("js/app/recipes.js");

        $this->view->searchQuery = $recipes[0]->getName();
        $this->view->page = $page;
        $this->view->orderId = $orderId;
        $this->view->recipeImage = '/uploads/images/recipes/';
    }

    public function ajaxnamesAction($orderId = NULL){

        if ($this->request->isAjax()) {

            $this->view->disable();

            if($orderId != NULL){
                $orderLab = DentistOrder::findFirst('code = '.$orderId);
            }
            $labDentist = LabDentists::find(array(
                'dentist_id = :dentist:',
                'bind' => array(
                    'dentist' => ($this->currentUser->Organisation->getOrganisationTypeId() == 4) ? $orderLab->getDentistId() : $this->currentUser->getOrganisationId()
                ),
                'columns' => 'lab_id'
            ))->toArray();

            $posibleArr = $this->findPossibleCategoriesAndRecipes($labDentist);

            $recipes = Recipes::find(array(
                'deleted_at IS NULL AND active = 1 AND id IN (' . implode(',', $posibleArr['recipes']) . ') ',
            ));

            $recipesNameArr = array();
            foreach ($recipes as $recipe) {
                $recipeName = is_null($recipe->ParentRecipe->getCustomName()) ? $recipe->ParentRecipe->getName() : $recipe->ParentRecipe->getCustomName();

                if (!in_array($recipeName, $recipesNameArr)) {
                    $recipesNameArr[] = ['number' => $recipe->ParentRecipe->getRecipeNumber(), 'realid' => $recipe->getId(), 'realname' => $recipeName, 'description' => $recipe->ParentRecipe->getDescription(), 'name' => (($recipe->ParentRecipe->getRecipeNumber() != NULL) ? $recipe->ParentRecipe->getRecipeNumber()." - " : "") . $recipeName . (($recipe->ParentRecipe->getDescription() != NULL) ? " - ".$recipe->ParentRecipe->getDescription() : "")];
                }
            }
            return json_encode($recipesNameArr);
        }
    }

    private function rebuildIdsForCalculation($customFieldPost){

        $newCustomFieldPost = [];

        foreach ($customFieldPost as $recipeDataId => $value) {

            /** @var DentistOrderRecipeData $dord */
            $dord = DentistOrderRecipeData::findFirstById($recipeDataId);

            if ($dord->RecipeCustomField) {
                $newCustomFieldPost[$dord->RecipeCustomField->getId()] = $value;
            }
        }
        return $newCustomFieldPost;
    }

    private function checkChild($childs, $currentId, $startS = false, $posibleArr)
    {
        if ($startS == true) {

            $this->existingchild = false;
        }

        if (count($childs) != 0) {

            foreach ($childs as $child) {

                $id = $child->getId();

                if (count($child->Recipes) == 0) {

                    $c = CategoryTree::find(
                        [
                            "parent_id = '$id'",
                            "order" => "sort"
                        ]
                    );
                    return $this->checkChild($c, $id, false, $posibleArr);

                }
                else {
                    if ($this->checkActiveChildren($child, $posibleArr)) {
                        $this->existingchild = true;
                    }
                }
            }
        }
        else {
            $c = CategoryTree::findFirst(
                [
                    "id = '$currentId'"
                ]
            );

            if ($this->checkActiveChildren($c, $posibleArr)) {

                $this->existingchild = true;
            }
        }
        return $this->existingchild;
    }

    private function checkActiveChildren($c, $posibleArr){

        foreach ($c->Recipes as $recipe) {

            foreach ($recipe->getActiveChildren() as $recipe_active) {

                if (in_array($recipe_active->getId(), $posibleArr['recipes'])) {
                    return true;
                }
            }
        }
        return false;
    }

    private function connectedCategoriesWithLab($labDentist){

        $resultArr = array();
        $connectedLab = array();

        foreach ($labDentist as $item) {

            $connectedLab[] = (int)$item['lab_id'];
        }

        $recipes = Recipes::find(array(
            'lab_id IN ({lab:array})',
            'bind' => array(
                'lab' => $connectedLab
            ),
            'columns' => 'id'
        ));

        if (count($recipes) > 0) {

            $recipeIds = array();

            foreach ($recipes as $item) {

                $recipeIds[] = $item['id'];
            }
            $categoryTreeRecipes = CategoryTreeRecipes::find(array(
                'recipe_id IN ({recipeIds:array})',
                'bind' => array(
                    'recipeIds' => $recipeIds
                ),
                'columns' => 'category_tree_id'
            ))->toArray();

            if (count($categoryTreeRecipes) > 0) {

                $categoryTreeIds = array();

                foreach ($categoryTreeRecipes as $item) {

                    if (!in_array($item['category_tree_id'], $categoryTreeIds)){
                        $categoryTreeIds[] = $item['category_tree_id'];
                    }
                }

                $secondCategory = CategoryTree::find(array(
                    'id IN ({categoryTreeIds:array})',
                    'bind' => array(
                        'categoryTreeIds' => $categoryTreeIds
                    ),
                    'columns' => 'parent_id',
                    "order" => "sort"
                ))->toArray();

                $secondCategoryIds = array();

                foreach ($secondCategory as $item) {

                    if (!in_array($item['parent_id'], $secondCategoryIds)){
                        $secondCategoryIds[] = $item['parent_id'];
                    }
                }

                $firstCategory = CategoryTree::find(array(
                    'id IN ({categoryTreeIds:array})',
                    'bind' => array(
                        'categoryTreeIds' => $secondCategoryIds
                    ),
                    'columns' => 'parent_id',
                    "order" => "sort"
                ))->toArray();

                $firstCategoryIds = array();

                foreach ($firstCategory as $item) {

                    if (!in_array($item['parent_id'], $firstCategoryIds)){
                        $firstCategoryIds[] = $item['parent_id'];
                    }
                }
                $resultArr = array_merge($categoryTreeIds, $secondCategoryIds, $firstCategoryIds);
                return $resultArr;
            }
        }
        return $resultArr;
    }

    private function generateCatTreeBreadCrumbs($id){

        if ($id != 0) {

            $owner = CategoryTree::findFirst($id);
            $this->breadCrumb[] = [
                "name" => $owner->getName(),
                "id" => $owner->getId()
            ];
            $parentId = $owner->getParentId();

            if ($parentId != 0) {

                return $this->generateCatTreeBreadCrumbs($parentId);
            }
            else {
                $done = array_reverse($this->breadCrumb, false);
                return $done;
            }
        }
    }

    private function orderNotificationContent(DentistOrder $order){

        $html = 'Open deze order in <a href=&quot;/lab/sales_order/view/' . $order->getCode() . '&quot;>het bestel-overzicht</a>';
        return $html;
    }
}
