<?php

namespace Signa\Controllers\Lab;

use Phalcon\Mvc\Model\Query;
use Signa\Models\ContactPersons;
use Signa\Models\DentistLocation;
use Signa\Models\DentistLocationClient;
use Signa\Models\Countries;
use Signa\Models\Invites;
use Signa\Models\InvoiceInfo;
use Signa\Models\LabPaymentArrangements;
use Signa\Models\Organisations;
use Signa\Models\CategoryTree;
use Signa\Models\CategoryTreeRecipes;
use Signa\Models\Recipes;
use Signa\Models\Users;
use Signa\Models\LabDentists;
use Signa\Models\CodeTariff;
use Signa\Helpers\User as UserHelper;
use Signa\Helpers\Translations as Trans;
use Signa\Helpers\General as HGeneral;

class SalesClientController extends InitController
{
    public function indexAction(){

        // (Invitation form)
        // Check if form is sent
        if($this->request->isPost()){

            $post = $this->request->getPost();

            // Search for existing users with same email
            $user = Users::findFirst("email LIKE '".$post['email']."'");

            // Search for registered users with same email
            $registered = Invites::findFirst("email LIKE '%".$post['email']."%' AND registered = 1 AND inviter_organisation = '".$this->currentUser->Organisation->getId()."'");

            // If no registered users
            if($registered === false){

                // Search for previous invites
                $previousInvites = Invites::find("email LIKE '%".$post['email']."%' AND deleted = '0' AND inviter_organisation = '".$this->currentUser->Organisation->getId()."'");

                // If invites found, update and soft delete
                if(count($previousInvites) > 0){
                    foreach($previousInvites as $pi){
                        $pi->setUpdatedAt(date("Y-m-d H:i:s"));
                        $pi->setUpdatedBy($this->currentUser->getId());
                        $pi->setDeleted(1);
                        $pi->save();
                    }
                }

                // If user found
                if($user !== false){

                    // Invite data
                    $invite = [
                        "user_id" => $user->getId(),
                        "created_by" => $this->currentUser->getId(),
                        "unique_id" => sha1(date("Y-m-d H:i:s")),
                        "email" => $post['email'],
                        "organisation_data" => $post['organisation_name'],
                        "valid_till" => date("Y-m-d H:i:s", strtotime("+2 months")),
                        "sended" => 1,
                        "inviter_organisation" => $this->currentUser->Organisation->getId()
                    ];

                    // Urls & params for existing user email template
                    $acceptUrl = '/api/existinguserinvitation?email='.urlencode($post['email']).'&lab='.$this->currentUser->Organisation->getId().'&status=accept&name='.$post['organisation_name'].'&den='.$post['organisation_id'];
                    $declineUrl = $this->baseUrl.'/api/existinguserinvitation?status=decline';
                    $params = array("button"=> array('url'=>$acceptUrl, 'text'=>'Accept'), "lab_name" => $this->currentUser->Organisation->getName(), "logo" => $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/uploads/images/organisation/'.$this->currentUser->Organisation->getLogo());

                    // Create notification for internal message system
                    $this->notifications->addNotification(array(
                        'type' => 5,
                        'subject' => Trans::make("Invitation to cooperate with"). ' ' .$this->currentUser->Organisation->getName(),
                        'description' => self::inviteNotification($acceptUrl),
                    ), null, null, array($user->getId()));

                    // Send email
                    $sendEmail = $this->mail->send($user->getEmail(), Trans::make("Invitation to cooperate with"). ' ' .$this->currentUser->Organisation->getName(), 'inviteNewClient', $params);
                }
                else {
                    // Invite data
                    $invite = [
                        "created_by" => $this->currentUser->getId(),
                        "unique_id" => sha1(date("Y-m-d H:i:s")),
                        "email" => $post['email'],
                        "organisation_data" => $post['organisation_name'],
                        "valid_till" => date("Y-m-d H:i:s", strtotime("+2 months")),
                        "sended" => 1,
                        "inviter_organisation" => $this->currentUser->Organisation->getId()
                    ];
                    // Urls & params for new user email template
                    $acceptUrl = '/api/termsofuse?email='.urlencode($post['email']).'&lab='.$this->currentUser->Organisation->getId().'&status=accept&name='.$post['organisation_name'].'&den='.$post['organisation_id'];
                    $params = array("button"=> array('url'=>$acceptUrl, 'text'=>'Accept'), "lab_name" => $this->currentUser->Organisation->getName(), "logo" => $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/uploads/images/organisation/'.$this->currentUser->Organisation->getLogo());
                    $sendEmail = $this->mail->send($post['email'], Trans::make("Invitation to cooperate with"). ' ' .$this->currentUser->Organisation->getName(), 'inviteNewClient', $params);
                }

                // If email was sent, create new invite
                if($sendEmail){
                    $newInvite = new Invites();
                    $newInvite->save($invite);

                    // Update dentist connection with lab
                    $pendingDentist = LabDentists::findFirst('dentist_id ='.$post['organisation_id'].' AND lab_id ='.$this->currentUser->Organisation->getId());
                    $pendingDentist->setStatus('pending');
                    $pendingDentist->save();
                }

                $result = json_encode(array(
                    'status' => (bool)$sendEmail,
                    'email' => $post['email'],
                    'user' => $user,
                    'sended' => $sendEmail,
                    'invite' => count($previousInvites)
                ));
            }
            else {
                $result = json_encode(array(
                    'status' => false,
                    'email' => $post['email'],
                    'user' => $user
                ));
            }
            return $result; // Return json data
        }

        // View vars
        $this->view->active = array($this->t->make('No'),$this->t->make('Yes'));
        $this->view->clients = LabDentists::find('lab_id = '.$this->currentUser->Organisation->getId());
        $this->view->inviteContent = $this->inviteContent();
    }

    public function addAction($kvk = NULL){

        if($kvk == NULL){
            $this->session->set('message', array('type' => 'error', 'content' => Trans::make('Kvk value must be a number')));
            $this->response->redirect("/lab/sales_client/");
            return;
        }

        // If kvk is not null then check for existing user
        $existingUser = false;
        $isUserActive = false;

        $output = self::validatekvk($this->currentUser->getOrganisationId(), $kvk);

        if($output['isKvkUsedWithinLab'] == 1){
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Client already exists')));
            $this->response->redirect("/lab/sales_client/view/".$output['dentistData']['id']);
        }
        elseif($output['isKvkUsedWithinLab'] == 0 && $output['isKvkUsed'] == 1){
            $existingUser = Organisations::findFirst('kvk_number = '.$kvk);
            $labConnections = LabDentists::find('dentist_id = '.$existingUser->getId());

            foreach ($labConnections as $lc){

                if($lc->getStatus() == 'active'){
                    $isUserActive = true;
                }
            }
        }

        // New dentist form
        if($this->request->isPost()){

            // Post data var allocation
            $orgData = $this->request->getPost('organisation');
            $labDentistData = $this->request->getPost('lab_dentist');
//            $contactDeleted = $this->request->getPost('person_deleted');
//            $contactEdited = $this->request->getPost('person_old');
            $contactNew = $this->request->getPost('person_added');
            $locationNew = $this->request->getPost('location_added');
            $locationDeleted = $this->request->getPost('location_deleted');
            $locationEdited = $this->request->getPost('location_old');
            $invoiceData = $this->request->getPost('invoice_info');
            $clientNumberData = $this->request->getPost('client_number');

            // Search for all dentists within the lab
            $check = LabDentists::find('lab_id = '.$this->currentUser->getOrganisationId());

            // If mail exists return message
            foreach($check as $mail){

                if($mail->Dentist){
                    if($mail->Dentist->getEmail() == $orgData['email']){
                        $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Email already exists')));
                        return;
                    }
                }
                else {
                    continue;
                }
            }

            // Create organisation if it doesn't exists
            if($existingUser == false) {

                // Create new organisation
                $newDentist = new Organisations();
                $newDentist->setName($orgData['name']);
                $newDentist->setOrganisationTypeId(3);
                $newDentist->setCountryId((!empty($orgData['country_id'])) ? $orgData['country_id'] : 16);
                $newDentist->setZipcode((!empty($orgData['zipcode'])) ? $orgData['zipcode'] : NULL);
                $newDentist->setCity((!empty($orgData['city'])) ? $orgData['city'] : NULL);
                $newDentist->setTelephone((!empty($orgData['telephone'])) ? $orgData['telephone'] : NULL);
                $newDentist->setAddress((!empty($orgData['address'])) ? $orgData['address'] : NULL);
                $newDentist->setActive(0);
                $newDentist->setEmail($orgData['email']);
                $newDentist->setKvkNumber($orgData['kvk_number']);
                $newDentist->save();
            }
            $dentistId = ($existingUser == false) ? $newDentist->getId() : $existingUser->getId();

            // Create new locations if more were added
            foreach($locationNew as $k => $v){
                $newLocation = new DentistLocation();
                $newLocation->setDentistId($dentistId);
                $newLocation->setCountryId($v['country_id']);
                $newLocation->setName($v['name']);
                $newLocation->setAddress(($v['address'] != '') ? $v['address'] : NULL);
                $newLocation->setCity(($v['city'] != '') ? $v['city'] : NULL);
                $newLocation->setZipcode(($v['zipcode'] != '') ? $v['zipcode'] : NULL);
                $newLocation->setTelephone(($v['telephone'] != '') ? $v['telephone'] : NULL);
//                $newLocation->setClientNumber(($v['client_number'] != '') ? $v['client_number'] : NULL);
                $newLocation->save();
            }

            // Edit existing locations if only editing
            foreach($locationEdited as $k => $v){
                $editLocation = DentistLocation::findFirst('id = '.$v['id']);
                $editLocation->setCountryId($v['country_id']);
                $editLocation->setName($v['name']);
                $editLocation->setAddress(($v['address'] != '') ? $v['address'] : NULL);
                $editLocation->setCity(($v['city'] != '') ? $v['city'] : NULL);
                $editLocation->setZipcode(($v['zipcode'] != '') ? $v['zipcode'] : NULL);
                $editLocation->setTelephone(($v['telephone'] != '') ? $v['telephone'] : NULL);
//                $editLocation->setClientNumber(($v['client_number'] != '') ? $v['client_number'] : NULL);
                $editLocation->save();
            }

            // Remove deleted locations
            foreach($locationDeleted as $k => $v){
                if($v == 1){
                    $deleteLocation = DentistLocation::findFirst('id='.$k);
                    $deleteLocation->softDelete();
                }
                else {
                    $enableLocation = DentistLocation::findFirst('id='.$k);
                    $enableLocation->setDeletedAt(NULL);
                    $enableLocation->setDeletedBy(NULL);
                    $enableLocation->save();
                }
            }

            // Handle client number data
            foreach($clientNumberData as $k => $v){

                $checkCN = DentistLocationClient::findFirst('lab_id = '.$this->currentUser->getOrganisationId().' AND location_id = '.$k);

                if($checkCN != false){

                    $checkCN->setClientNumber($v);
                    $checkCN->save();
                }
                else {
                    $newClientNumber = new DentistLocationClient();
                    $newClientNumber->setLabId($this->currentUser->getOrganisationId());
                    $newClientNumber->setLocationId($k);
                    $newClientNumber->setClientNumber($v);
                    $newClientNumber->save();
                }
            }

            // Check if there is invoice data in the form
            if($invoiceData){

                // Search for invoice data within this dentist
                $invoiceInfo = InvoiceInfo::findFirst('dentist_id = '.$dentistId);

                // If no invoice data then add new, else edit existing
                if($invoiceInfo == false){
                    $newInfo = new InvoiceInfo();
                    $newInfo->setLabId($this->currentUser->getOrganisationId());
                    $newInfo->setDentistId($dentistId);
                    $newInfo->setAddress((!empty($invoiceData['address'])) ? $invoiceData['address'] : NULL);
                    $newInfo->setZipcode((!empty($invoiceData['zipcode'])) ? $invoiceData['zipcode'] : NULL);
                    $newInfo->setCity((!empty($invoiceData['city'])) ? $invoiceData['city'] : NULL);
                    $newInfo->setEmail((!empty($invoiceData['email'])) ? $invoiceData['email'] : NULL);
                    $newInfo->setContactAdmin((!empty($invoiceData['contact_admin'])) ? $invoiceData['contact_admin'] : NULL);
                    $newInfo->setTelephoneAdmin((!empty($invoiceData['telephone_admin'])) ? $invoiceData['telephone_admin'] : NULL);
                    $newInfo->setBankAccount((!empty($invoiceData['bank_account'])) ? $invoiceData['bank_account'] : NULL);
                    $newInfo->setCountryId($invoiceData['country_id']);
                    $newInfo->setSalutation($invoiceData['salutation']);
                    $newInfo->save();
                }
                else {
                    $invoiceInfo->setAddress((!empty($invoiceData['address'])) ? $invoiceData['address'] : NULL);
                    $invoiceInfo->setZipcode((!empty($invoiceData['zipcode'])) ? $invoiceData['zipcode'] : NULL);
                    $invoiceInfo->setCity((!empty($invoiceData['city'])) ? $invoiceData['city'] : NULL);
                    $invoiceInfo->setEmail((!empty($invoiceData['email'])) ? $invoiceData['email'] : NULL);
                    $invoiceInfo->setContactAdmin((!empty($invoiceData['contact_admin'])) ? $invoiceData['contact_admin'] : NULL);
                    $invoiceInfo->setTelephoneAdmin((!empty($invoiceData['telephone_admin'])) ? $invoiceData['telephone_admin'] : NULL);
                    $invoiceInfo->setBankAccount((!empty($invoiceData['bank_account'])) ? $invoiceData['bank_account'] : NULL);
                    $invoiceInfo->setCountryId($invoiceData['country_id']);
                    $invoiceInfo->setSalutation($invoiceData['salutation']);
                    $invoiceInfo->save();
                }
            }

            // Create new contact persons
            foreach($contactNew as $k => $v){
                $newPerson = new ContactPersons();
                $newPerson->setLabId($this->currentUser->getOrganisationId());
                $newPerson->setDentistId($dentistId);
                $newPerson->setName($v['name']);
                $newPerson->setPhone($v['phone']);
                $newPerson->setEmail($v['email']);
                $newPerson->setFunction($v['function']);
                $newPerson->save();
            }

            // Create new connection between lab and dentist
            $newDentistLab = new LabDentists();
            $newDentistLab->setLabId($this->currentUser->getOrganisationId());
            $newDentistLab->setDentistId($dentistId);
            $newDentistLab->setClientPreferences((!empty($labDentistData['client_preferences'])) ? $labDentistData['client_preferences'] : NULL);
            $newDentistLab->setClientNumber((!empty($labDentistData['client_number'])) ? $labDentistData['client_number'] : NULL);
            $newDentistLab->setPaymentArrangementId((!empty($labDentistData['payment_arrangement'])) ? $labDentistData['payment_arrangement'] : NULL);

            // Check for uploaded files
            if ($this->request->hasFiles() == true) {
                $imgDir = $this->config->application->labContractsDir;
                $randomString = HGeneral::randomString(6);
                $file = $this->request->getUploadedFiles()[0];
                $fileName = $randomString.$file->getName();

                if($file->getRealType() == 'application/pdf')
                {
                    if(!is_dir($imgDir)) {
//                        mkdir($imgDir, 0777);
                        mkdirR($imgDir, 0777);
                    }
                    $file->moveTo($imgDir.$fileName);
                    chmod($imgDir, 0777);
                    chmod($imgDir.$fileName, 0777);
                    $newDentistLab->setContract($fileName);
                }
            }
            $newDentistLab->setStatus('concept');
            $newDentistLab->save();

            // Success/error messages
            if($newDentistLab->save()){

                $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Dentist added successfully')));
                $this->response->redirect("/lab/sales_client/");
            }
            else {
                $this->session->set('message', array('type' => 'error', 'content' => Trans::make('Error while creating dentist')));
            }
        }

        // View vars & assets
        $this->assets->collection('additional')->addJs('bower_components/tinymce/tinymce.min.js');
//        $this->view->contactPersons = ($existingUser != false) ? ContactPersons::find('dentist_id = '.$existingUser->getId().' AND deleted_at IS NULL') : NULL;
        $this->view->locations = ($existingUser != false) ? DentistLocation::find('dentist_id = '.$existingUser->getId()) : NULL;
        $this->view->isUserActive = $isUserActive;
        $this->view->countryList = Countries::find();
        $this->view->kvk = $kvk;
        $this->view->organisation = ($existingUser != false) ? $existingUser : NULL;
        $this->view->labDentistData = ($existingUser != false) ? LabDentists::findFirst('dentist_id = '.$existingUser->getId().' AND lab_id = '.$this->currentUser->getOrganisationId()) : NULL;
        $this->view->paymentArrangements = LabPaymentArrangements::find('lab_id = '.$this->currentUser->getOrganisationId().' AND deleted_at IS NULL');
    }

    public function viewAction($id){

        // Search for dentist connected to the lab
        $labDentist = LabDentists::findFirst('dentist_id = '.$id.' AND lab_id = '.$this->currentUser->getOrganisationId());

        // View dentist form
        if($this->request->isPost()){

            // Post data var allocation
            $locationEdited = $this->request->getPost('location');
            $orgData = $this->request->getPost('organisation');
            $labDentistData = $this->request->getPost('lab_dentist');
            $contactDeleted = $this->request->getPost('person_deleted');
            $contactEdited = $this->request->getPost('person_old');
            $contactNew = $this->request->getPost('person_added');
            $clientNumberData = $this->request->getPost('client_number');

            // Edit lab dentist
            $labDentist->setClientNumber((empty($labDentistData['client_number']) ? NULL : $labDentistData['client_number']));
            $labDentist->setClientPreferences((empty($labDentistData['client_preferences']) ? NULL : $labDentistData['client_preferences']));

            // Check for uploaded files
            if ($this->request->hasFiles() == true) {
                $imgDir = $this->config->application->labContractsDir;
                $randomString = HGeneral::randomString(6);
                $file = $this->request->getUploadedFiles()[0];
                $fileName = $randomString.$file->getName();

                if($file->getRealType() == 'application/pdf')
                {
                    if(!is_dir($imgDir)) {
//                        mkdir($imgDir, 0777);
                        mkdirR($imgDir, 0777);
                    }
                    $file->moveTo($imgDir.$fileName);
                    chmod($imgDir, 0777);
                    chmod($imgDir.$fileName, 0777);
                    $labDentist->setContract($fileName);
                }
            }

            $labDentist->save();

            // Handle client number data
            foreach($clientNumberData as $k => $v){

                $checkCN = DentistLocationClient::findFirst('lab_id = '.$this->currentUser->getOrganisationId().' AND location_id = '.$k);

                if($checkCN != false){

                    $checkCN->setClientNumber($v);
                    $checkCN->save();
                }
                else {
                    $newClientNumber = new DentistLocationClient();
                    $newClientNumber->setLabId($this->currentUser->getOrganisationId());
                    $newClientNumber->setLocationId($k);
                    $newClientNumber->setClientNumber($v);
                    $newClientNumber->save();
                }
            }

            // Edit existing locations if only editing
            foreach($locationEdited as $k => $v){
                $editLocation = DentistLocation::findFirst('id = '.$v['id']);
                $editLocation->setClientNumber(($v['client_number'] != '') ? $v['client_number'] : NULL);
                $editLocation->save();
            }

            // Create new contact persons
            foreach($contactNew as $k => $v){
                $newPerson = new ContactPersons();
                $newPerson->setLabId($this->currentUser->getOrganisationId());
                $newPerson->setDentistId($id);
                $newPerson->setName($v['name']);
                $newPerson->setPhone($v['phone']);
                $newPerson->setEmail($v['email']);
                $newPerson->setFunction($v['function']);
                $newPerson->save();
            }

            // Edit existing contact persons if only editing
            foreach($contactEdited as $k => $v){
                $editPerson = ContactPersons::findFirst('id = '.$v['id']);
                $editPerson->setName($v['name']);
                $editPerson->setPhone($v['phone']);
                $editPerson->setEmail($v['email']);
                $editPerson->setFunction($v['function']);
                $editPerson->save();
            }

            // Remove deleted contact persons
            foreach($contactDeleted as $k => $v){
                if($v == 1){
                    $deletePerson = ContactPersons::findFirst('id='.$k);
                    $deletePerson->softDelete();
                }
            }
        }

        $clientNumber = [];

        foreach(DentistLocation::find('dentist_id = '.$id)->toArray() as $l){

            $tmp_DLC = DentistLocationClient::find('lab_id = '.$this->currentUser->getOrganisationId().' AND location_id = '.$l['id'])->toArray();
            $l['client_number'] = (count($tmp_DLC) > 0) ? $tmp_DLC[0]['client_number'] : NULL;
            $clientNumber[$l['id']] = $l['client_number'];
        }

        // View & asset vars
        $this->view->labDentistData = LabDentists::findFirst('dentist_id = '.$id.' AND lab_id = '.$this->currentUser->getOrganisationId());
        $this->view->organisation = $labDentist->Dentist;
        $this->view->contactPersons = ContactPersons::find('dentist_id = '.$id.' AND deleted_at IS NULL');
        $this->view->organisation = Organisations::findFirst("id = '".$id."'");
        $this->view->locations = DentistLocation::find('dentist_id = '.$id);
        $this->view->countryList = Countries::find();
        $this->view->id = $id;
        $this->view->clientNumber = $clientNumber;
    }

    public function editAction($id){

        // Search for connections with other labs and check if user is still in concept or active
        $labConnections = LabDentists::find('dentist_id = '.$id);
        $isUserActive = false;

        foreach ($labConnections as $lc){

            if($lc->getStatus() == 'active'){
                $isUserActive = true;
            }
        }

        // Search for dentist connected to the lab
        $labDentist = LabDentists::findFirst('dentist_id = '.$id.' AND lab_id = '.$this->currentUser->getOrganisationId());

        // Search for tariff codes assigned to the lab
        $codes = CodeTariff::find('organisation_id = '.$this->currentUser->getOrganisationId());

        // Search for recipes available to the lab
        $recipes = Recipes::find('lab_id = '.$this->currentUser->getOrganisationId());

        // (Edit dentist form)
        // Check if form is sent
        if($this->request->isPost()){

            // Post data var allocation
            $orgData = $this->request->getPost('organisation');
            $contactDeleted = $this->request->getPost('person_deleted');
            $contactEdited = $this->request->getPost('person_old');
            $contactNew = $this->request->getPost('person_added');
            $locationDeleted = $this->request->getPost('location_deleted');
            $locationEdited = $this->request->getPost('location_old');
            $locationNew = $this->request->getPost('location_added');
            $invoiceData = $this->request->getPost('invoice_info');
            $labDentistData = $this->request->getPost('lab_dentist');
            $clientTariffData = $this->request->getPost('client_tariff');
            $clientRecipeData = $this->request->getPost('client_recipe');
            $clientNumberData = $this->request->getPost('client_number');

            // Search and edit dentist organisation
            $labDentist->Dentist->setName($orgData['name']);
            $labDentist->Dentist->setEmail($orgData['email']);
            $labDentist->Dentist->setKvkNumber($orgData['kvk_number']);
            $labDentist->Dentist->setZipcode((empty($orgData['zipcode']) ? NULL : $orgData['zipcode']));
            $labDentist->Dentist->setAddress((empty($orgData['address']) ? NULL : $orgData['address']));
            $labDentist->Dentist->setTelephone((empty($orgData['telephone']) ? NULL : $orgData['telephone']));
            $labDentist->Dentist->setCity((empty($orgData['city']) ? NULL : $orgData['city']));
            $labDentist->Dentist->setCountryId((empty($orgData['country_id']) ? NULL : $orgData['country_id']));
            $labDentist->Dentist->save();

            // Edit lab-dentist related data

//            $labDentist->setClientPreferencesTariff((empty($clientTariffData) ? NULL : json_encode($clientTariffData)));
//            $labDentist->setClientPreferencesRecipe((empty($clientRecipeData) ? NULL : json_encode($clientRecipeData)));

            // If files attached, save
            if ($this->request->hasFiles() == true) {
                $imgDir = $this->config->application->labContractsDir;
                $randomString = HGeneral::randomString(6);
                $file = $this->request->getUploadedFiles()[0];
                $fileName = $randomString.$file->getName();

                if($file->getRealType() == 'application/pdf'){
                    if(!is_dir($imgDir)) {
                        mkdirR($imgDir, 0777);
                    }
                    $file->moveTo($imgDir.$fileName);
                    chmod($imgDir.$fileName, 0777);
                    $labDentist->setContract($fileName);
                }
            }
            $labDentist->setClientNumber((empty($labDentistData['client_number']) ? NULL : $labDentistData['client_number']));
            $labDentist->setClientPreferences((empty($labDentistData['client_preferences']) ? NULL : $labDentistData['client_preferences']));
            $labDentist->setPaymentArrangementId((empty($labDentistData['payment_arrangement']) ? NULL : $labDentistData['payment_arrangement']));
            $labDentist->save();

            // Create new contact persons if more were added
            foreach($contactNew as $k => $v){
                $newPerson = new ContactPersons();
                $newPerson->setLabId($this->currentUser->getOrganisationId());
                $newPerson->setDentistId($id);
                $newPerson->setName($v['name']);
                $newPerson->setPhone($v['phone']);
                $newPerson->setEmail($v['email']);
                $newPerson->setFunction($v['function']);
                $newPerson->save();
            }

            // Edit existing contact persons if only editing
            foreach($contactEdited as $k => $v){
                $editPerson = ContactPersons::findFirst('id = '.$v['id']);
                $editPerson->setName($v['name']);
                $editPerson->setPhone($v['phone']);
                $editPerson->setEmail($v['email']);
                $editPerson->setFunction($v['function']);
                $editPerson->save();
            }

            // Remove deleted contact persons
            foreach($contactDeleted as $k => $v){
                if($v == 1){
                    $deletePerson = ContactPersons::findFirst('id='.$k);
                    $deletePerson->softDelete();
                }
            }

            // Create new locations if more were added
            foreach($locationNew as $k => $v){
                $newLocation = new DentistLocation();
                $newLocation->setDentistId($id);
                $newLocation->setCountryId($v['country_id']);
                $newLocation->setName($v['name']);
                $newLocation->setAddress(($v['address'] != '') ? $v['address'] : NULL);
                $newLocation->setCity(($v['city'] != '') ? $v['city'] : NULL);
                $newLocation->setZipcode(($v['zipcode'] != '') ? $v['zipcode'] : NULL);
                $newLocation->setTelephone(($v['telephone'] != '') ? $v['telephone'] : NULL);
                $newLocation->setClientNumber(($v['client_number'] != '') ? $v['client_number'] : NULL);
                $newLocation->save();
            }

            // Edit existing locations if only editing
            foreach($locationEdited as $k => $v){
                $editLocation = DentistLocation::findFirst('id = '.$v['id']);
                $editLocation->setCountryId($v['country_id']);
                $editLocation->setName($v['name']);
                $editLocation->setAddress(($v['address'] != '') ? $v['address'] : NULL);
                $editLocation->setCity(($v['city'] != '') ? $v['city'] : NULL);
                $editLocation->setZipcode(($v['zipcode'] != '') ? $v['zipcode'] : NULL);
                $editLocation->setTelephone(($v['telephone'] != '') ? $v['telephone'] : NULL);
                $editLocation->setClientNumber(($v['client_number'] != '') ? $v['client_number'] : NULL);
                $editLocation->save();
            }

            // Remove deleted locations
            foreach($locationDeleted as $k => $v){
                if($v == 1){
                    $deleteLocation = DentistLocation::findFirst('id='.$k);
                    $deleteLocation->softDelete();
                }
                else {
                    $enableLocation = DentistLocation::findFirst('id='.$k);
                    $enableLocation->setDeletedAt(NULL);
                    $enableLocation->setDeletedBy(NULL);
                    $enableLocation->save();
                }
            }

            // Check if there is invoice data in the form
            if($invoiceData){

                // Search for invoice data within this dentist
                $invoiceInfo = InvoiceInfo::findFirst('dentist_id = '.$id);

                // If no invoice data then add new, else edit existing
                if($invoiceInfo == false){
                    $newInfo = new InvoiceInfo();
                    $newInfo->setLabId($this->currentUser->getOrganisationId());
                    $newInfo->setDentistId($id);
                    $newInfo->setAddress((!empty($invoiceData['address'])) ? $invoiceData['address'] : NULL);
                    $newInfo->setZipcode((!empty($invoiceData['zipcode'])) ? $invoiceData['zipcode'] : NULL);
                    $newInfo->setCity((!empty($invoiceData['city'])) ? $invoiceData['city'] : NULL);
                    $newInfo->setEmail((!empty($invoiceData['email'])) ? $invoiceData['email'] : NULL);
                    $newInfo->setContactAdmin((!empty($invoiceData['contact_admin'])) ? $invoiceData['contact_admin'] : NULL);
                    $newInfo->setTelephoneAdmin((!empty($invoiceData['telephone_admin'])) ? $invoiceData['telephone_admin'] : NULL);
                    $newInfo->setBankAccount((!empty($invoiceData['bank_account'])) ? $invoiceData['bank_account'] : NULL);
                    $newInfo->setCountryId($invoiceData['country_id']);
                    $newInfo->setSalutation($invoiceData['salutation']);
                    $newInfo->save();
                }
                else {
                    $invoiceInfo->setAddress((!empty($invoiceData['address'])) ? $invoiceData['address'] : NULL);
                    $invoiceInfo->setZipcode((!empty($invoiceData['zipcode'])) ? $invoiceData['zipcode'] : NULL);
                    $invoiceInfo->setCity((!empty($invoiceData['city'])) ? $invoiceData['city'] : NULL);
                    $invoiceInfo->setEmail((!empty($invoiceData['email'])) ? $invoiceData['email'] : NULL);
                    $invoiceInfo->setContactAdmin((!empty($invoiceData['contact_admin'])) ? $invoiceData['contact_admin'] : NULL);
                    $invoiceInfo->setTelephoneAdmin((!empty($invoiceData['telephone_admin'])) ? $invoiceData['telephone_admin'] : NULL);
                    $invoiceInfo->setBankAccount((!empty($invoiceData['bank_account'])) ? $invoiceData['bank_account'] : NULL);
                    $invoiceInfo->setCountryId($invoiceData['country_id']);
                    $invoiceInfo->setSalutation($invoiceData['salutation']);
                    $invoiceInfo->save();
                }
            }

            // Success/error messages
            if($labDentist->save()){

                $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Client data updated successfully')));
                $this->response->redirect("/lab/sales_client/");
            }
            else {
                $this->session->set('message', array('type' => 'error', 'content' => Trans::make('Error while updating client data')));
            }
        }

        $clientNumber = [];

        foreach(DentistLocation::find('dentist_id = '.$id)->toArray() as $l){

            $tmp_DLC = DentistLocationClient::find('lab_id = '.$this->currentUser->getOrganisationId().' AND location_id = '.$l['id'])->toArray();
            $l['client_number'] = (count($tmp_DLC) > 0) ? $tmp_DLC[0]['client_number'] : NULL;
            $clientNumber[$l['id']] = $l['client_number'];
        }

        // View & asset vars
        $this->assets->collection('additional')->addJs('bower_components/tinymce/tinymce.min.js');
//        $this->view->tariff_preferences = json_decode($labDentist->getClientPreferencesTariff(), true);
//        $this->view->recipe_preferences = json_decode($labDentist->getClientPreferencesRecipe(), true);
        $this->view->isUserActive = $isUserActive;
        $this->view->codes = $codes;
        $this->view->recipes = $recipes;
        $this->view->lab_dentist = $labDentist;
        $this->view->contactPersons = ContactPersons::find('dentist_id = '.$id.' AND lab_id = '.$this->currentUser->getOrganisationId().' AND deleted_at IS NULL');
        $this->view->locations = $labDentist->Dentist->DentistLocations;
        $this->view->countryList = Countries::find();
        $this->view->organisation = $labDentist->Dentist;
        $this->view->id = $id;
        $this->view->paymentArrangements = LabPaymentArrangements::find('lab_id = '.$this->currentUser->getOrganisationId().' AND deleted_at IS NULL');
        $this->view->clientNumber = $clientNumber;
    }

    public function editinviteAction($id){

        // Search for invite
        $invite = Invites::findFirst('id='.$id);

        // Check if ajax request
        if($this->request->isAjax()){

            $invite->setClientNumber($this->request->getPost("client_number"));

            // Check if number then save
            if(is_numeric($this->request->getPost("client_number"))){

                $invite->save();
                $result = json_encode(array(
                    'status' => 'ok',
                    'msg'   => Trans::make('Client number updated successfully')
                ));
            }
            else {
                $result = json_encode(array(
                    'status' => 'error',
                    'msg'   => Trans::make('Only numbers allowed')
                ));
            }
            return $result;
        }

        // View vars
        $this->view->invite = $invite;
        $this->view->id = $id;
    }

    public function pendingAction(){

        $invites = Invites::find("registered = '0' AND deleted = '0' AND sended = '1' AND inviter_organisation = '".$this->currentUser->Organisation->getId()."' GROUP BY email ORDER BY created_at DESC");
        $this->view->invites = $invites;
    }

    public function deactivateAction($id){

        $user = Users::findFirst($id);

        if($user) {
            $user->deactivate();
            $this->session->set('message', array('type' => 'success', 'content' => Trans::make('User has been deactivated.')));
        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make("User doesn't exist.")));
        }
        $this->response->redirect(sprintf('/%s/sales_client/',$this->currentUser->Organisation->OrganisationType->getSlug()));
        $this->view->disable();
        return;
    }

    public function activateAction($id){

        $user = Users::findFirst($id);

        if($user) {
            $user->activate();
            $this->session->set('message', array('type' => 'success', 'content' => Trans::make('User has been activated.')));
        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make("User doesn't exist.")));
        }
        $this->response->redirect(sprintf('/%s/sales_client/',$this->currentUser->Organisation->OrganisationType->getSlug()));
        $this->view->disable();
        return;
    }

    /**
     * login as user without password - only for admins
     */
    public function loginasuserAction($userId){

        $user = Users::findFirst($userId);

        if (!$user) {
            throw new \Exception('User not exist');
        }

        $userHelper = new UserHelper($this->session, $this->cookies);
        $redirectUrl = '';

        try {
            $userHelper->logInAsUser($user, $this->currentUser);
            $redirectUrl = $this->access->getDefaultUrl();

        }
        catch (\Exception $e) {
            $this->view->error = $e->getMessage();
            return;
        }
        $this->response->redirect($redirectUrl);
    }

    /**
     * back to admin from logged user (from login without password)
     */
    public function backtoadminAction(){

        $userHelper = new UserHelper($this->session, $this->cookies);
        $admin = $userHelper->backToAdminUser();

        if ($admin->hasRole('ROLE_LAB_USER_MASTERKEY')) {

            $url = sprintf('%s/sales_client/masterkey', $admin->Organisation->OrganisationType->getSlug());
        }
        else {
            $url = sprintf('%s/sales_client/', $admin->Organisation->OrganisationType->getSlug());
        }
        $this->response->redirect($url);
    }

    public function deletecontractAction($id){

        $labDentist = LabDentists::findFirst('id='.$id);
        unlink($this->config->application->labContractsDir.$labDentist->getContract());
        $labDentist->setContract(NULL);
        $labDentist->save();

        if($labDentist->save()){
            $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Contract has been deleted')));
        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make("Error while deleting contract")));
        }
        $this->response->redirect('/lab/sales_client/edit/'.$labDentist->getDentistId());
        $this->view->disable();
        return;
    }

    public function validatekvkAction(){

        if ($this->request->isAjax() && $this->request->isPost()) {

            $this->view->disable();
            $post = $this->request->getPost();

            $isKvkUsedWithinLab = false;
            $isKvkUsed = false;
            $dentistData = [];

            $existingUser = Organisations::findFirst('kvk_number = '.$post['kvk_number']);

            if($existingUser != false){
                $isKvkUsed = true;
                $dentistData['name'] = $existingUser->getName();
                $dentistData['street'] = $existingUser->getAddress();
                $dentistData['postal'] = $existingUser->getZipcode();
                $dentistData['city'] = $existingUser->getCity();
            }
            $labDentist = LabDentists::find('lab_id = '.$post['lab_id']);

            foreach($labDentist as $den){

                if($den->Dentist && $post['kvk_number'] == $den->Dentist->getKvkNumber()){
                    $isKvkUsedWithinLab = true;

                }
            }
            $result = ["isKvkUsedWithinLab" => $isKvkUsedWithinLab, "isKvkUsed" => $isKvkUsed, "dentistData" => $dentistData];
            return json_encode($result);
        }
    }

    public function recipelistAction($id){

        //Lists for the view
        $categoryList = [];
        $availableCategories = CategoryTree::find();
        $availableRecipes = Recipes::find('active = 1 AND lab_id = '.$this->currentUser->getOrganisationId());
        $selectedRecipes = LabDentists::findFirst('lab_id = '.$this->currentUser->getOrganisationId().' AND dentist_id = '.$id);
        $dentist = Organisations::findFirst('id = '.$id);

        if($this->request->isPost()){

            if($this->request->getPost('recipe') != NULL){

                $activeRecipes = [];

                foreach($this->request->getPost('recipe') as $k => $v){
                    if($v == 1){
                        $activeRecipes[] = $k;
                    }
                }
                $selectedRecipes->setActiveRecipes(($activeRecipes == NULL) ? NULL : $activeRecipes);
                $selectedRecipes->save();

                if($selectedRecipes->save() !== false){
                    $this->session->set('message', ['type' => 'success', 'content' => Trans::make("Recipes saved")]);
                    $this->response->redirect('lab/sales_client/recipelist/'.$id);
                }
            }
        }

        foreach ($availableCategories as $cat){

            //If not a main category
            if($cat->getParentId() != 0){

                //If is sub-sub-category
                if($cat->ParentCategory->parent_id != 0){

                    // Allocate sub-sub-category
                    $categoryList[$cat->ParentCategory->parent_id]['sub_categories'][$cat->getParentId()]['sub_sub_categories'][$cat->getId()] = $cat->toArray();
                    $categoryList[$cat->ParentCategory->parent_id]['sub_categories'][$cat->getParentId()]['sub_sub_categories'][$cat->getId()]['show'] = 0;

                    foreach($availableRecipes as $ctr){

                        // If recipe is in category tree, then allocate recipe
                        if(CategoryTreeRecipes::findFirst('category_tree_id = '.$cat->getId().' AND recipe_id = '.$ctr->ParentRecipe->id)){
                            $categoryList[$cat->ParentCategory->parent_id]['sub_categories'][$cat->getParentId()]['sub_sub_categories'][$cat->getId()]['recipes'][$ctr->id] = $ctr->toArray();
                            $categoryList[$cat->ParentCategory->parent_id]['sub_categories'][$cat->getParentId()]['sub_sub_categories'][$cat->getId()]['show'] = 1;
                            $categoryList[$cat->ParentCategory->parent_id]['sub_categories'][$cat->getParentId()]['show'] = 1;
                            $categoryList[$cat->ParentCategory->parent_id]['show'] = 1;
                        }
                    }
                }
                else {
                    //If is sub-category
                    $categoryList[$cat->getParentId()]['sub_categories'][$cat->getId()] = $cat->toArray();
                    $categoryList[$cat->getParentId()]['sub_categories'][$cat->getId()]['show'] = 0;
                }
            }
            else {
                //Main categories
                $categoryList[$cat->getId()] = $cat->toArray();
                $categoryList[$cat->getId()]['show'] = 0;
            }
        }

//        _dump($selectedRecipes);exit();
        // View vars
        $this->view->categoryList = $categoryList;
        $this->view->selectedRecipes = $selectedRecipes->getActiveRecipes();
        $this->view->dentist = $dentist;
    }

    private static function inviteContent(){

        $html = '<p>'.Trans::make("By entering a dentist admin e-mailadres you'll be able to invite them. If this dentist isn' registered they will receive an email to create an account.").'</p>';
        $html .= '<div class="row"><div class="col-md-12"><div class="form-group"><label>'.Trans::make("Organisation name").'</label><input type="text" name="organisation_name" id="organisation_name" class="form-control"></div>';
        $html .= '<div class="form-group"><label>'.Trans::make("Admin E-mail").'</label><input type="email" name="email" id="email-value" class="form-control"></div></div></div>';

        return $html;
    }

    private static function inviteNotification($acceptUrl){

        $html = '<p>'.Trans::make("Lab invites you to be part of the group").'</p>';
        $html .= '<a href=&quot;'.$acceptUrl .'&quot;>'.Trans::make("Accept").'</a>';

        return $html;
    }

    private static function validatekvk($labId, $kvk){

        $isKvkUsedWithinLab = 0;
        $isKvkUsed = 0;
        $dentistData = [];

        $existingUser = Organisations::findFirst('kvk_number = '.$kvk);


        if($existingUser != false){
            $isKvkUsed = 1;
            $dentistData['id'] = $existingUser->getId();
            $dentistData['name'] = $existingUser->getName();
            $dentistData['street'] = $existingUser->getAddress();
            $dentistData['postal'] = $existingUser->getZipcode();
            $dentistData['city'] = $existingUser->getCity();
        }
        $labDentist = LabDentists::find('lab_id = '.$labId);

        foreach($labDentist as $den){

            if($den->Dentist && $kvk == $den->Dentist->getKvkNumber()){
                $isKvkUsedWithinLab = 1;

            }
        }
        $result = ["isKvkUsedWithinLab" => $isKvkUsedWithinLab, "isKvkUsed" => $isKvkUsed, "dentistData" => $dentistData];
        return $result;
    }
}
