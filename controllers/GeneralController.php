<?php

namespace Signa\Controllers;

use Signa\Helpers\Translations;
use Signa\Libs\Convert;
use Signa\Models\InvoiceInfo;
use Signa\Models\LabDentists;
use Signa\Models\LabPaymentArrangements;
use Signa\Models\OrganisationTypes;
use Signa\Models\SettingFiles;
use Signa\Models\Organisations;
use Signa\Models\Countries;
use Signa\Models\DentistLocation;
use Signa\Helpers\General as HGeneral;

class GeneralController extends ControllerBase
{
    public function initialize(){

        parent::initialize();
        $this->view->disableSubnav = true;
    }

    public function accountAction(){

        $logs = $this->mongoLogger->readLog(
            array('conditions' => array(
                'user' => $this->currentUser->getEmail()
            ))
        );
        $this->view->user = $this->currentUser;
        $this->view->logs = $logs;
    }

    public function organisationAction(){

        $this->view->organisation = $this->currentUser->Organisation;
        $this->view->users = $this->currentUser->Organisation->users;
    }

    public function organisationEditAction($id){

        // Get organisation
        $organisation = Organisations::findFirst($id);

        if ($this->request->isPost()){

            // Post data var allocation
            $orgData = $this->request->getPost('organisation');
            $invoiceData = $this->request->getPost('invoice_info');
            $locationDeleted = $this->request->getPost('location_deleted');
            $locationEdited = $this->request->getPost('location_old');
            $locationNew = $this->request->getPost('location_added');

            // Edit organisation
            $organisation->setName($orgData['name']);
            $organisation->setAddress($orgData['address']);
            $organisation->setEmail($orgData['email']);
            $organisation->setCity($orgData['city']);
            $organisation->setCountryId($orgData['country_id']);
            $organisation->setZipcode($orgData['zipcode']);
            $organisation->setTelephone($orgData['telephone']);
            $organisation->setInvoiceFooter($orgData['invoice_footer']);
            $organisation->setFinancialData($orgData['financial_data']);
            $organisation->setDeliveryNotes($orgData['delivery_notes']);

            if($organisation->InvoiceInfo){
                $organisation->InvoiceInfo->setAddress((!empty($invoiceData['address'])) ? $invoiceData['address'] : NULL);
                $organisation->InvoiceInfo->setZipcode((!empty($invoiceData['zipcode'])) ? $invoiceData['zipcode'] : NULL);
                $organisation->InvoiceInfo->setCity((!empty($invoiceData['city'])) ? $invoiceData['city'] : NULL);
                $organisation->InvoiceInfo->setEmail((!empty($invoiceData['email'])) ? $invoiceData['email'] : NULL);
                $organisation->InvoiceInfo->setContactAdmin((!empty($invoiceData['contact_admin'])) ? $invoiceData['contact_admin'] : NULL);
                $organisation->InvoiceInfo->setTelephoneAdmin((!empty($invoiceData['telephone_admin'])) ? $invoiceData['telephone_admin'] : NULL);
                $organisation->InvoiceInfo->setBankAccount((!empty($invoiceData['bank_account'])) ? $invoiceData['bank_account'] : NULL);
                $organisation->InvoiceInfo->setCountryId((!empty($invoiceData['country_id'])) ? $invoiceData['country_id'] : NULL);
                $organisation->InvoiceInfo->setSalutation((!empty($invoiceData['salutation'])) ? $invoiceData['salutation'] : NULL);
                $organisation->InvoiceInfo->save();
            }
            else {
                $invoiceInfo = new InvoiceInfo();
                $invoiceInfo->setDentistId($id);
                $invoiceInfo->setAddress((!empty($invoiceData['address'])) ? $invoiceData['address'] : NULL);
                $invoiceInfo->setZipcode((!empty($invoiceData['zipcode'])) ? $invoiceData['zipcode'] : NULL);
                $invoiceInfo->setCity((!empty($invoiceData['city'])) ? $invoiceData['city'] : NULL);
                $invoiceInfo->setEmail((!empty($invoiceData['email'])) ? $invoiceData['email'] : NULL);
                $invoiceInfo->setContactAdmin((!empty($invoiceData['contact_admin'])) ? $invoiceData['contact_admin'] : NULL);
                $invoiceInfo->setTelephoneAdmin((!empty($invoiceData['telephone_admin'])) ? $invoiceData['telephone_admin'] : NULL);
                $invoiceInfo->setBankAccount((!empty($invoiceData['bank_account'])) ? $invoiceData['bank_account'] : NULL);
                $invoiceInfo->setCountryId((!empty($invoiceData['country_id'])) ? $invoiceData['country_id'] : NULL);
                $invoiceInfo->setSalutation((!empty($invoiceData['salutation'])) ? $invoiceData['salutation'] : NULL);
                $invoiceInfo->save();
            }

            // Check for uploaded files
            if ($this->request->hasFiles() == true) {

                $imgDir = $this->config->application->organisationImagesDir;
                $randomString = HGeneral::randomString(6);
                $file = $this->request->getUploadedFiles()[0];
                $fileName = $randomString.$file->getName();

                if($file->getRealType() == 'image/jpeg' || $file->getRealType() == 'image/png'){

                    if(!is_dir($imgDir)){
                        mkdirR($imgDir);
                    }
                    $file->moveTo($imgDir.$fileName);

                    //generate thumbnails
                    $this->imageThumb->createAllSizes($imgDir.$fileName);
                    $organisation->setLogo($fileName);
                }
            }

            // Create new locations if were added
            foreach($locationNew as $k => $v){
                $newLocation = new DentistLocation();
                $newLocation->setDentistId($id);
                $newLocation->setCountryId($v['country_id']);
                $newLocation->setName($v['name']);
                $newLocation->setAddress(($v['address'] != '') ? $v['address'] : NULL);
                $newLocation->setCity(($v['city'] != '') ? $v['city'] : NULL);
                $newLocation->setZipcode(($v['zipcode'] != '') ? $v['zipcode'] : NULL);
                $newLocation->setTelephone(($v['telephone'] != '') ? $v['telephone'] : NULL);
                $newLocation->save();
            }

            // Edit existing locations
            foreach($locationEdited as $k => $v){
                $editLocation = DentistLocation::findFirst('id = '.$v['id']);
                $editLocation->setCountryId($v['country_id']);
                $editLocation->setName($v['name']);
                $editLocation->setAddress(($v['address'] != '') ? $v['address'] : NULL);
                $editLocation->setCity(($v['city'] != '') ? $v['city'] : NULL);
                $editLocation->setZipcode(($v['zipcode'] != '') ? $v['zipcode'] : NULL);
                $editLocation->setTelephone(($v['telephone'] != '') ? $v['telephone'] : NULL);
                $editLocation->save();
            }

            // Disable & re-enable locations
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
            $organisation->save();

            // Success/error messages
            if ($organisation->save()) {

                $transString = Translations::make("Organisation has been edited");
                $this->session->set('message', ['type' => 'success', 'content' => $transString . '.']);
                $this->response->redirect('general/organisation');
                $this->view->disable();
                return;
            }
            else {
                $transString = Translations::make("Organisation can't be edited");
                $this->session->set('message', ['type' => 'error', 'content' => $transString . "."]);
            }
        }

        // View and asset vars
        if($organisation->getOrganisationTypeId() == 3){

            $this->view->labDentists = LabDentists::find('dentist_id='.$id);
            $this->view->locations = DentistLocation::find('dentist_id = '.$id);
        }
        $this->assets->collection('additional')->addJs('bower_components/tinymce/tinymce.min.js');
        $this->view->countryList = Countries::find();
        $this->view->organisationTypes = Convert::toIdArray(OrganisationTypes::find(array("order" => "name")));
        $this->view->organisation = $organisation;
        $this->view->users = $this->currentUser->Organisation->users;
        $this->view->payments = LabPaymentArrangements::find('lab_id = '.$this->currentUser->getOrganisationId().' AND deleted_at IS NULL');
    }

    public function preferencesAction(){

        $currentUserId = $this->currentUser->getId();
        $settingsUserFile = SettingFiles::find('to_user_id = '.$currentUserId.' AND from_user_id IS NOT NULL');

        if($this->request->isPost()){

            $userFiles = $this->request->getPost('userFile');
            $usersArr = array();

            foreach ($settingsUserFile as $settingFile){

                if(in_array($settingFile->getId(), $userFiles)){

                    $settingFile->setAllow(1);
                }
                else {
                    $settingFile->setAllow(0);
                }
                $settingFile->save();
                $usersArr[] = $settingFile->getId();
            }
            $this->session->set('message', ['type' => 'success', 'content' => 'Successfully save data.']);
            $this->response->redirect('/general/preferences/');
            return true;
        }
        $this->assets->collection('footer')
            ->addJs("js/bootstrap/tab.js");
        $this->view->userFiles = $settingsUserFile;
    }
}
