<?php

namespace Signa\Controllers\Lab;

use Signa\Helpers\Date;
use Signa\Helpers\Translations;
use Signa\Libs\DentistOrders;
use Signa\Models\DentistGroupDiscount;
use Signa\Models\DentistOrder;
use Signa\Models\DentistOrderNotes;
use Signa\Models\DentistOrderFile;
use Signa\Models\DentistOrderRecipeData;
use Signa\Models\DentistOrderRecipeDataOptions;
use Signa\Models\LabDentists;
use Signa\Models\MapLabTariffLedger;
use Signa\Models\Organisations;
use Signa\Models\DentistOrder as DentistOrderModel;
use Signa\Models\DentistOrderBsn;
use Signa\Models\DentistOrderData;
use Signa\Models\DentistOrderRecipe;
use Signa\Models\CodeTariff;
use Signa\Models\RecipeCustomField;
use Signa\Models\RecipeCustomFieldOptions;
use Signa\Models\Recipes;
use Phalcon\Mvc\View;
use Signa\Helpers\Translations as Trans;
use Signa\Models\RecipeStatus;
use Signa\Models\Users;
use Signa\Models\DeliveryNotes;
use Signa\Models\DentistLocation;

class SalesOrderController extends InitController
{
    public function indexAction(){

        $this->view->locations = DentistLocation::find('dentist_id = '.$this->currentUser->getOrganisationId());
        $this->view->orders = DentistOrders::getOrdersConfirmedByLab($this->currentUser->getOrganisationId());
    }

    public function historyAction(){

        $this->view->locations = DentistLocation::find('dentist_id = '.$this->currentUser->getOrganisationId());
        $this->view->orders = DentistOrders::getOrdersInDeliveryByLab($this->currentUser->getOrganisationId());
    }

    public function incomingAction(){

        $this->view->locations = DentistLocation::find('dentist_id = '.$this->currentUser->getOrganisationId());
        $this->view->orders = DentistOrders::getOrdersIncomingByLab($this->currentUser->getOrganisationId());
    }

    public function allAction(){

        $this->view->locations = DentistLocation::find('dentist_id = '.$this->currentUser->getOrganisationId());
        $this->view->orders = DentistOrders::getOrdersByLab($this->currentUser->getOrganisationId());
    }

    public function updateAction(){

        if ($this->request->isAjax() && $this->request->isPost()) {

            $this->view->disable();
            $recipe = DentistOrderRecipe::findFirst($this->request->getPost('id'));
            $recipe->setSchemaValues(json_encode($this->request->getPost('schema_values')));

            if($recipe->save()){

                $result = json_encode([
                    "status"    => "ok",
                    "msg"       =>  Trans::make("Schema updated")
                ]);
            }
            else {
                $result = json_encode([
                    "status"    => "error",
                    "msg"       =>  Trans::make("Error updating schema")
                ]);
            }
            return $result;
        }
    }

    public function editAction(){

        if ($this->request->isAjax() && $this->request->isPost()) {

            $this->view->disable();

            $originalRecipe = DentistOrderRecipe::findFirst('recipe_id='.$this->request->getPost('oldRecipe').' AND order_id='.$this->request->getPost('orderId'));

            if($originalRecipe->getDeletedAt() != NULL){

                $childrenRecipe = DentistOrderRecipe::findFirst('parent_id='.$originalRecipe->getId().' AND order_id='.$this->request->getPost('orderId').' ORDER BY created_at DESC');
                $childrenRecipe->setDeletedAt(date('Y-m-d H:i:s'));
                $childrenRecipe->setDeletedBy($this->currentUser->getOrganisationId());
                $childrenRecipe->save();
            }
            else {
                $originalRecipe->setDeletedAt(date('Y-m-d H:i:s'));
                $originalRecipe->setDeletedBy($this->currentUser->getOrganisationId());
                $originalRecipe->save();
            }

            $newRecipe = new DentistOrderRecipe();
            $newRecipe->setOrderId($this->request->getPost('orderId'));
            $newRecipe->setRecipeId($this->request->getPost('newRecipe'));
            $newRecipe->setParentId($originalRecipe->getId());
            $newRecipe->setPrice(0.00);
            $newRecipe->save();

            $recipeData = Recipes::findFirst('id='.$this->request->getPost('newRecipe'));

            foreach ($recipeData->ParentRecipe->RecipeCustomField as $customField){

                $newRecipeData = new DentistOrderRecipeData();
                $newRecipeData->setOrderRecipeId($newRecipe->getId());
                $newRecipeData->setRecipeCustomFieldId($customField->getId());
                $newRecipeData->setFieldName($customField->getName());
                $newRecipeData->setFieldType($customField->getType());
                $newRecipeData->setCustomPriceTariffId($customField->getCustomPriceTariffId());
                $newRecipeData->setCustomPriceType($customField->getCustomPriceType());
                $newRecipeData->setHasLabCheck($customField->getHasLabCheck());
                $newRecipeData->setAmount($customField->getAmount());
                $newRecipeData->save();

                if(in_array($customField->getType(), ['select', 'checkbox'])){

                    foreach ($customField->Options as $option){

                        $newRecipeDataOption = new DentistOrderRecipeDataOptions();
                        $newRecipeDataOption->setDentistOrderRecipeDataId($newRecipeData->getId());
                        $newRecipeDataOption->setOption($option->getOption());
                        $newRecipeDataOption->setValue($option->getValue());
                        $newRecipeDataOption->setTariffId($option->getTariffId());
                        $newRecipeDataOption->setCustomPriceTariffId($option->getCustomPriceTariffId());
                        $newRecipeDataOption->save();
                    }
                }
            }

            if($newRecipe->save()){
                $result = json_encode([
                    "status"    => "ok",
                    "msg"       =>  Trans::make("Order updated")
                ]);
            }
            else {
                $result = json_encode([
                    "status"    => "error",
                    "msg"       =>  Trans::make("Error updating order")
                ]);
            }
            return $result;
        }
    }

    public function viewAction($code){

        $order = DentistOrder::findFirst('code ='.$code);
        $orderBy = Users::findFirst($order->getCreatedBy());
        $orgData = Organisations::findFirst($order->getDentistId());
        $orderRecipes = DentistOrderRecipe::find('order_id = '.$order->getId().' AND price IS NOT NULL');

        if (!$order) {

            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make("Order doesn't exist.")));
            $this->response->redirect('/lab/sales_order/');
            $this->view->disable();
            return;
        }

        if ($this->request->isAjax() && $this->request->isPost()) {

            $this->view->disable();
            $post = $this->request->getPost();
            $recipe = DentistOrderRecipe::findFirst($post['id']);
            $recipe->setStatus($post['newStatus']);
            $recipe->setStatusChangedBy($this->currentUser->getId());
            $recipe->setStatusChangedAt(Date::currentDatetime());
            $recipe->setStatusPrev((empty($post['orgStatus'])) ? NULL : $post['orgStatus']);

            if($recipe->save()){
                return json_encode($recipe);
            }
            else {
                return json_encode('error');
            }
            die;
        }

        /* @var CodeTariff[] $tariffs */
        $tariffs = CodeTariff::find(
            array(
                'active = :active: AND organisation_id = :organisation_id:',
                'bind' => array(
                    'active' => 1,
                    'organisation_id' => $this->currentUser->getOrganisationId()
                )
            )
        );

        /** @var $map MapLabTariffLedger */
        /** @var CodeTariff[] $mappedSignaTariffs */
        /** @var CodeTariff[] $mappedSignaTariffsReverse */
        $mappedSignaTariffs = [];
        $mappedSignaTariffsReverse = [];

        foreach ($tariffs as $tariff) {

            $map = MapLabTariffLedger::findFirstByTariffId($tariff->getId());

            if ($map) {

                $mappedSignaTariffs[$map->getSignaTariffId()] = $tariff;

                if ($map->SignaTariff) {

                    $mappedSignaTariffsReverse[$map->getTariffId()] = $map->SignaTariff;
                }
            }
        }

        $this->view->tariffs = $tariffs;
        $this->view->mappedSignaTariffs = $mappedSignaTariffs;
        $this->view->mappedSignaTariffsReverse = $mappedSignaTariffsReverse;

        if ($this->request->isPost()) {

            foreach ($this->request->getPost() as $key => $value){

                if (in_array($key, ['pricetmp', 'disabled'])) {
                    continue;
                }

                if($key == 'recipe_data'){

                    foreach ($value as $recipeKey => $recipeData){

                        foreach ($recipeData as $customFieldKey => $customFieldVal) {

                            if (is_int($customFieldKey)) {

                                /** @var DentistOrderRecipeData $editField */
                                $editField = DentistOrderRecipeData::findFirst('order_recipe_id = ' . $recipeKey . ' AND id = ' . $customFieldKey);

                                if ($customFieldVal['type'] == 'checkbox') {

                                    $editField->setFieldValue(json_encode($customFieldVal['options']));

                                    foreach ($customFieldVal['tariff'] as $optionKey => $optionTariff) {

                                        $editTariff = DentistOrderRecipeDataOptions::findFirst('id = ' . $optionKey);
                                        $editTariff->setTariffId((!empty($optionTariff)) ? $optionTariff : NULL);
                                        $editTariff->setTariffOptions($customFieldVal['tariff_options'][$optionKey]);
                                        $editTariff->save();
                                    }
                                }
                                elseif ($customFieldVal['type'] == 'select') {

                                    $editField->setFieldValue($customFieldVal['options']);
                                    $editTariff = DentistOrderRecipeDataOptions::findFirst('dentist_order_recipe_data_id = ' . $customFieldKey . ' AND value = "' . $customFieldVal['options'] . '"');
                                    $editTariff->setTariffId($customFieldVal['tariff']);
                                    $editTariff->setTariffOptions($customFieldVal['tariff_options']);
                                    $editTariff->save();
                                }
                                else {
                                    $editField->setFieldValue($customFieldVal['options']);
                                    $editField->setCustomPriceTariffId($customFieldVal['tariff']);
                                    $editField->setTariffOptions($customFieldVal['tariff_options']);
                                }
                                $editField->setAmount($customFieldVal['amount']);
                                $editField->save();
                            }
                            else {
                                if ($customFieldVal['type'] == 'textarea' || $customFieldVal['type'] == 'text') {

                                    $editField = DentistOrderRecipeData::findFirst('order_recipe_id = ' . $recipeKey . ' AND id = ' . $customFieldKey);
                                    $editField->setFieldValue($customFieldVal['options']);
                                    $editField->setAmount($customFieldVal['amount']);
                                    $editField->save();
                                }
                            }
                        }
                    }
                }
                elseif($key == 'order_data'){

                    $postData = $this->request->getPost('order_data');
                    $order->DentistOrderData->setPatientInitials(!empty($postData['patient_initials']) ? $postData['patient_initials'] : NULL);
                    $order->DentistOrderData->setPatientInsertion(!empty($postData['patient_insertion']) ? $postData['patient_insertion'] : NULL);
                    $order->DentistOrderData->setPatientLastname(!empty($postData['patient_lastname']) ? $postData['patient_lastname'] : NULL);
                    $order->DentistOrderData->setPatientGender(!empty($postData['patient_gender']) ? $postData['patient_gender'] : NULL);
                    $order->DentistOrderData->setPatientNumber(!empty($postData['patient_number']) ? $postData['patient_number'] : NULL);

                    if($postData['patient_birth']['year'] != "-" && $postData['patient_birth']['month'] != "-" && $postData['patient_birth']['day'] != "-"){

                        $order->DentistOrderData->setPatientBirth($postData['patient_birth']['year']."-".$postData['patient_birth']['month']."-".$postData['patient_birth']['day']);
                    }
                    else {
                        $order->DentistOrderData->setPatientBirth(NULL);
                    }
                    $order->DentistOrderBsn->setBsn(!empty($postData['bsn']) ? $postData['bsn'] : NULL);
                    $order->save();
                }
            }
            $orderNoteId = null;
            $description = $this->request->getPost('new_message');

            if(isset($description)){

                if(!empty($description)){

                    $orderNotes = new DentistOrderNotes();
                    $orderNotes->setNote($description);
                    $orderNotes->setOrderId($order->getId());
                    $orderNotes->save();
                    $orderNoteId = $orderNotes->getId();
                }
            }

            foreach ($this->request->getPost('pricetmp') as $orderRecipeToUpdateId => $newPrice) {

                /** @var DentistOrderRecipe $orderRecipeToUpdate */
                $orderRecipeToUpdate = DentistOrderRecipe::findFirstById($orderRecipeToUpdateId);

                if (!$orderRecipeToUpdate) {
                    continue;
                }
                $orderRecipeToUpdate->setPrice($newPrice);
                $orderRecipeToUpdate->save();
            }

            if ($this->request->hasFiles() == true) {

                $configDir = $this->config->application->dentistOrderDir;

                // Print the real file names and their sizes
                foreach ($this->request->getUploadedFiles() as $file) {

                    if(!empty($file->getName())){

                        $imgDir = $configDir;

                        if(!is_dir($imgDir)) {
                            mkdir($imgDir, 0777);
                        }
                        $file->moveTo($configDir.$file->getName());
                        $orderFile = new DentistOrderFile();
                        $orderFile->setOrderId($order->getId());
                        $orderFile->setFileName($file->getName());
                        $orderFile->setFilePath($configDir);
                        $orderFile->setFileType($file->getType());
                        $orderFile->setOrderNoteId((empty($orderNoteId) || $orderNoteId == NULL) ? NULL : $orderNoteId);
                        $orderFile->save();
                    }
                    else {
                        continue;
                    }
                }
            }
            $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Message has been added.')));
            $this->response->redirect('/lab/sales_order/view/'.$code);
        }
        $attachments = DentistOrderFile::find([
            'order_id = :orderId:',
            'bind' => [
                'orderId' => $order->getId()
            ]
        ]);

        $schema = array();

        foreach ($order->DentistOrderRecipe as $do){

            if($do->schema_values != NULL){

                $raw_values = json_decode($do->schema_values);

                for($j=18;$j>=11;$j--){
                    $upper_left[$j] = '<td><span data-tooth="'.$j.'">'.$j.'</span><input id="T'.$j.'_'.$do->id.'" class="upper" name="teeth[T'.$j.'_'.$do->id.']" type="checkbox" value="0"><label class="upper" for="T'.$j.'" data-id="'.$do->id.'"></label></td>';
                }

                for($k=21;$k<=28;$k++){
                    if($k == 21){
                        $upper_right[$k] = '<td class="divider"><span data-tooth="'.$k.'">'.$k.'</span><input id="T'.$k.'_'.$do->id.'" class="upper" name="teeth[T'.$k.'_'.$do->id.']" type="checkbox" value="0"><label class="upper" for="T'.$k.'" data-id="'.$do->id.'"></label></td>';
                    }
                    else {
                        $upper_right[$k] = '<td><span data-tooth="'.$k.'">'.$k.'</span><input id="T'.$k.'_'.$do->id.'" class="upper" name="teeth[T'.$k.'_'.$do->id.']" type="checkbox" value="0"><label class="upper" for="T'.$k.'" data-id="'.$do->id.'"></label></td>';
                    }
                }

                for($m=48;$m>=41;$m--){
                    $lower_left[$m] = '<td><input id="T'.$m.'_'.$do->id.'" class="lower" name="teeth[T'.$m.'_'.$do->id.']" type="checkbox" value="0"><label class="lower" for="T'.$m.'" data-id="'.$do->id.'"></label><span data-tooth="'.$m.'">'.$m.'</span></td>';
                }

                for($n=31;$n<=38;$n++){
                    if($n == 31){
                        $lower_right[$n] = '<td class="divider"><input id="T'.$n.'_'.$do->id.'" class="lower" name="teeth[T'.$n.'_'.$do->id.']" type="checkbox" value="0"><label class="lower" for="T'.$n.'" data-id="'.$do->id.'"></label><span data-tooth="'.$n.'">'.$n.'</span></td>';
                    }
                    else {
                        $lower_right[$n] = '<td><input id="T'.$n.'_'.$do->id.'" class="lower" name="teeth[T'.$n.'_'.$do->id.']" type="checkbox" value="0"><label class="lower" for="T'.$n.'" data-id="'.$do->id.'"></label><span data-tooth="'.$n.'">'.$n.'</span></td>';
                    }
                }

                $schema[$do->id] = [
                    'id' => $do->id,
                    'raw_values'    => $raw_values,
                    'upper_left'    => $upper_left,
                    'upper_right'   => $upper_right,
                    'lower_left'    => $lower_left,
                    'lower_right'   => $lower_right
                ];
            }
        }

        if($order->DentistOrderData->patient_birth != NULL){
            $birthDate = [
                "day" => date("d", strtotime($order->DentistOrderData->patient_birth)),
                "month" => date("m", strtotime($order->DentistOrderData->patient_birth)),
                "year" => date("Y", strtotime($order->DentistOrderData->patient_birth))
            ];
        }

        $availableRecipes = Recipes::find('active = 1 AND organisation_id='.$this->currentUser->getOrganisationId());
        $this->assets->collection('additional')
            ->addJs('bower_components/tinymce/tinymce.min.js');
        $this->assets->collection('footer')
            ->addJs("js/app/accounting.min.js")
            ->addJs("js/app/incomingOrders.js");

        $this->view->birthDate = $birthDate;
        $this->view->lab_dentists = LabDentists::find('lab_id = '.$this->currentUser->getOrganisationId());
        $this->view->locations = DentistLocation::find('dentist_id = '.$order->getDentistId());
        $this->view->currentLabDentist = LabDentists::findFirst('lab_id = '.$this->currentUser->getOrganisationId().' AND dentist_id = '.$order->getDentistId());
        $this->view->statuses_av = RecipeStatus::find()->toArray();
        $this->view->counter = 0;
        $this->view->schema = $schema;
        $this->view->messages = DentistOrderNotes::find('order_id = '.$order->getId());
        $this->view->order = $order;
        $this->view->orderRecipes = $orderRecipes;
        $this->view->availableRecipes = $availableRecipes;
        $this->view->attachments = $attachments;
        $this->view->orderBy = $orderBy;
        $this->view->organisation = $orgData;
        $this->view->disableSubnav = true;
    }

    public function getdiscountpriceAction($id){

        /* @var $dgd DentistGroupDiscount */
        $dgd = DentistGroupDiscount::findFirst(
            [
                'code = :code:',
                'bind' => [
                    'code' => $id
                ]
            ]
        );
    }

    public function todeliveryAction($code){

        $order = null;
        $basic = false;
        $orders = DentistOrders::getOrdersByLab($this->currentUser->Organisation->getId());

        foreach ($orders as $orderCheck) {

            if ($code == $orderCheck->getCode()) {

                $order = $orderCheck;

                foreach($order->DentistOrderRecipe as $recipe){

                    if($recipe->Recipes->getIsBasic() == 1 || $recipe->Recipes->ParentRecipe->getIsBasic() == 1){

                        $basic = true;
                        break;
                    }
                }
                break;
            }
        }

        if (!$order || $order->getStatus() != 3 || $basic == true) {

            if($basic == true){
                $this->session->set('message', array('type' => 'error', 'content' => Trans::make("You can not complete an order with a basic recipe on it")));
            }
            else {
                $this->session->set('message', array('type' => 'warning', 'content' => Trans::make("Order doesn't exist.")));
            }

            $this->response->redirect('/lab/sales_order/');
            $this->view->disable();
            return;
        }

        if($order->DeliveryNote){

            if($order->DeliveryNote->getStatus() == 'concept'){

                $order->DeliveryNote->setStatus('confirmed');
                $order->DeliveryNote->save();
            }
        }
        else {
            $amountNotes = count(DeliveryNotes::find('lab_id ='.$this->currentUser->Organisation->getId()));

            $deliveryNote = new DeliveryNotes();
            $deliveryNote->setOrderId($order->getId());
            $deliveryNote->setOrderDentistId($order->Dentist->getId());
            $deliveryNote->setLabId($this->currentUser->Organisation->getId());
            $deliveryNote->setDeliveryNumber($amountNotes+1);
            $deliveryNote->setStatus('confirmed');
            $deliveryNote->save();
        }

        $order->setStatus(4);
        $order->save();

        $this->notifications->addNotification(array(
            'type' => 7,
            'subject' => Trans::make('Order moved to delivery'),
            'description' => $this->orderNotificationContent($order)
        ),null, $order->CreatedBy->Organisation->getId());

        $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Order has been moved to delivery.')));
        $this->response->redirect('/lab/sales_order/history');
        $this->view->disable();
    }

    public function toinprogressAction($code){

        $order = null;
        $orders = DentistOrders::getOrdersByLab($this->currentUser->Organisation->getId());

        foreach ($orders as $orderCheck) {

            if ($code == $orderCheck->getCode()) {
                $order = $orderCheck;
                break;
            }
        }

        if (!$order || $order->getStatus() != 2) {

            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make("Order doesn't exist.")));
            $this->response->redirect('/lab/sales_order/');
            $this->view->disable();
            return;
        }

        $order->setStatus(3);
        $order->save();

        $this->notifications->addNotification(array(
            'type' => 7,
            'subject' => Trans::make('Order has been moved to In progress'),
            'description' => $this->orderNotificationContent($order)
        ),null, $order->CreatedBy->Organisation->getId());

        $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Order has been moved to In progress')));
        $this->response->redirect('/lab/sales_order/view/'.$code);
        $this->view->disable();
    }

    public function downloadAction($id){

        $dentistOrderFile = DentistOrderFile::findFirst($id);
        $fileName = $dentistOrderFile->getFileName();
        $filePath = $dentistOrderFile->getFilePath();
        $file = $filePath . $fileName;

        if (file_exists($file)) {
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
            $this->session->set('message', array('type' => 'warning', 'content' => 'Attachment doesn\'t exist.'));
            $this->response->redirect('/lab/sales_order/');
        }
    }

    public function printlabelAction($code, $dentistId){

        $order = DentistOrder::findFirst('deleted_at IS NULL AND dentist_id = '.$dentistId.' AND code = '.$code);

        $this->view->render('pdf', 'print_label', [
            'lab' => $order->DentistOrderRecipe[0]->Recipes->Lab,
            'dentist' => $order->Dentist,
            'order' => $order
        ]);
    }

    public function pdflabelAction($code, $dentistId){

        $this->view->disable();

        $order = DentistOrder::findFirst('deleted_at IS NULL AND dentist_id = '.$dentistId.' AND code = '.$code);

        $html = $this->view->getRender('pdf', 'pdf_label', [
            'lab' => $order->DentistOrderRecipe[0]->Recipes->Lab,
            'dentist' => $order->Dentist,
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
        $mpdf->Output("label_".$order->getCode().'.pdf', "I");
    }

    public function printAction($code){

        $order = null;
        $orgData = null;
        $orderBy = null;
        $name = Trans::make("Order no.").' '.$code.'.pdf';

        $orders = DentistOrders::getOrdersByLab($this->currentUser->Organisation->getId());

        foreach ($orders as $orderCheck) {

            if ($code == $orderCheck->getCode()) {

                $order = $orderCheck;
                $orderBy = Users::findFirst($order->getCreatedBy());
                $orgData = Organisations::findFirst($order->getDentistId());
                break;
            }
        }

        if (!$order) {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make("Order doesn't exist.")));
            $this->response->redirect('/lab/sales_order/view/'.$code);
            $this->view->disable();
            return;
        }

        foreach($order->DentistOrderRecipe as $r){

            $schema[$r->id] = json_decode($r->schema_values);
        }

        $labDentist = LabDentists::findFirst('lab_id = '.$this->currentUser->getOrganisationId().' AND dentist_id = '.$order->getDentistId());

        $this->view->render('print', 'dentist_order_print', [
            'messages'      => DentistOrderNotes::find('order_id = '.$order->getId()),
            'order'         => $order,
            'orderBy'       => $orderBy,
            'organisation'  => $orgData,
            'schema'        => $schema,
            'labDentist'    => $labDentist
        ]);
    }

    public function getpdfAction($code){

        $order = null;
        $orgData = null;
        $orderBy = null;
        $name = Trans::make("Order no.").' '.$code.'.pdf';

        $orders = DentistOrders::getOrdersByLab($this->currentUser->Organisation->getId());

        foreach ($orders as $orderCheck) {

            if ($code == $orderCheck->getCode()) {

                $order = $orderCheck;
                $orderBy = Users::findFirst($order->getCreatedBy());
                $orgData = Organisations::findFirst($order->getDentistId());
                break;
            }
        }

        if (!$order) {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make("Order doesn't exist.")));
            $this->response->redirect('/lab/sales_order/view/'.$code);
            $this->view->disable();
            return;
        }

        foreach($order->DentistOrderRecipe as $r){

            $schema[$r->id] = json_decode($r->schema_values);
        }

        $labDentist = LabDentists::findFirst('lab_id = '.$this->currentUser->getOrganisationId().' AND dentist_id = '.$order->getDentistId());

        $view = clone $this->view;
        $view->start();
        $view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $view->setVars(array(
            'messages'      => DentistOrderNotes::find('order_id = '.$order->getId()),
            'order'         => $order,
            'orderBy'       => $orderBy,
            'organisation'  => $orgData,
            'schema'        => $schema,
            'labDentist'    => $labDentist
        ));
        $view->render('pdf','dentist_order'); // volt template file
        $view->finish();
        $html = $view->getContent();

        $pdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'tempDir' => $this->config->application->cacheDir,
            'setAutoTopMargin' => 'stretch'
        ]);

        $stylesheet = file_get_contents(__DIR__.'/../../../public/css/main.css');
        $stylesheet2 = file_get_contents(__DIR__.'/../../../public/css/pdf/pdforder.css');
        $pdf->WriteHTML($stylesheet.$stylesheet2,1);
        $pdf->WriteHTML($html,2);
        $pdf->Output($name, "I");
        die;
    }

    private function orderNotificationContent(DentistOrder $order){

        $html = Trans::make('Your order').' '.$order->getCode().' '.Trans::make('has been moved to delivery');
        $html .= '<br />';
        $html .= Trans::make('Open this order in').' <a href=&quot;/dentist/order/view/'.$order->getCode().'&quot;>Orderlist</a>';

        return $html;
    }
}
