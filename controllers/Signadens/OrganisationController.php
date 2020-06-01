<?php

namespace Signa\Controllers\Signadens;

use Signa\Helpers\Translations;
use Signa\Models\DentistGroupDiscount;
use Signa\Models\SupplierInfo;
use Signa\Models\Users;
use Signa\Models\Organisations;
use Signa\Models\OrganisationTypes;
use Signa\Models\Countries;
use Signa\Libs\Convert;
use Signa\Helpers\General;

class OrganisationController extends InitController
{
	const ORGANISATION_TYPE_SUPPLIER = 1;

    public function indexAction(){

        $this->view->organisations = Organisations::find(array('deleted_at IS NULL'));
        $this->view->active = array($this->t->make('No'),$this->t->make('Yes'));
    }

    public function addAction(){

        if ($this->request->isPost()){

            $organisation = new Organisations();
            $supplierInfo = new SupplierInfo();

            if (!isset($this->request->getPost('organisation')['is_group'])){
                $organisation->setIsGroup(null);
            }

            if ($this->request->getPost('organisation')['organisation_type_id'] == self::ORGANISATION_TYPE_SUPPLIER) {

                $supplierInfo->saveForm($this->request->getPost('supplier_info'));

                if ($supplierInfo->save() === false) {

                    $transString = Translations::make("Suppliers Info for this organisation can't be edited");
                    $this->session->set('message', ['type' => 'error', 'content' => $transString . "."]);
                }
            }
            $organisation->SupplierInfo = $supplierInfo;
            $organisation->saveForm($this->request->getPost('organisation'));

            if ($this->request->hasFiles() == true) {

                $imgDir = $this->config->application->organisationImagesDir;
                $randomString = General::randomString(6);
                $file = $this->request->getUploadedFiles()[0];
                $fileName = $randomString.$file->getName();

                if($file->getRealType() == 'image/jpeg' || $file->getRealType() == 'image/png'){

                    if(!is_dir($imgDir)) {
                        mkdirR($imgDir, 0777);
                    }
                    $file->moveTo($imgDir.$fileName);

                    //generate thumbnails
                    $this->imageThumb->createAllSizes($imgDir.$fileName);
                    $organisation->setLogo($fileName);
                }
            }

            if ($organisation->save() !== false) {

                $transString = Translations::make("New organisation has been added");
                $this->session->set('message', ['type' => 'success', 'content' => $transString.'.']);
                $this->response->redirect('signadens/organisation/');
                $this->view->disable();
                return;
            }
            else {
                $transString = Translations::make("Organisation can't be added");
                $this->session->set('message', ['type' => 'error', 'content' => $transString . "."]);
            }
        }
        $countries = Countries::find(array("order" => "name"));
        $this->view->countries = Convert::toIdArray($countries);
        $organisationTypes = OrganisationTypes::find(array("order" => "name"));
        $this->view->organisationTypes = Convert::toIdArray($organisationTypes);
        $this->view->active = array($this->t->make('No'),$this->t->make('Yes'));
    }

    public function editAction($id){

        $organisation = Organisations::findFirst($id);
        $supplierInfo = $organisation->SupplierInfo;

        if($supplierInfo === false) {

            $supplierInfo = new SupplierInfo();
            $supplierInfo->setOrganisationId($id);
        }

        if ($this->request->isPost()){

            if ($this->request->getPost('organisation')['organisation_type_id'] != self::ORGANISATION_TYPE_SUPPLIER) {
                $supplierInfo->delete();
            }

            if (!isset($this->request->getPost('organisation')['is_group'])){

                $organisation->setIsGroup(null);
                $dgd = DentistGroupDiscount::find('organisation_id = '.$organisation->getId());
                $dgd->delete();
            }

            $organisation->saveForm($this->request->getPost('organisation'));

            if ($this->request->getPost('organisation')['organisation_type_id'] == self::ORGANISATION_TYPE_SUPPLIER) {

                $supplierInfo->saveForm($this->request->getPost('supplier_info'));

                if ($supplierInfo->save() === false) {

                    $transString = Translations::make("Suppliers Info for this organisation can't be edited");
                    $this->session->set('message', ['type' => 'error', 'content' => $transString . "."]);
                }
            }

            if ($this->request->hasFiles() == true) {

                $imgDir = $this->config->application->organisationImagesDir;
                $randomString = General::randomString(6);
                $file = $this->request->getUploadedFiles()[0];
                $fileName = $randomString.$file->getName();

                if($file->getRealType() == 'image/jpeg' || $file->getRealType() == 'image/png'){

                    if(!is_dir($imgDir)) {

                        mkdirR($imgDir);
                    }
                    $file->moveTo($imgDir.$fileName);

                    //generate thumbnails
                    $this->imageThumb->createAllSizes($imgDir.$fileName);
                    $organisation->setLogo($fileName);
                }
            }

            if ($organisation->save() !== false) {

                if($organisation->getActive() == 0){

                    $organisationUsers = $organisation->users;

                    foreach ($organisationUsers as $organisationUser){

                        $organisationUser->setActive(0);
                        $organisationUser->save();
                    }
                }
                $transString = Translations::make("Organisation has been edited");
                $this->session->set('message', ['type' => 'success', 'content' => $transString . '.']);
                $this->response->redirect('signadens/organisation/edit/'.$id);
                $this->view->disable();
                return;
            }
            else {
                $transString = Translations::make("Organisation can't be edited");
                $this->session->set('message', ['type' => 'error', 'content' => $transString . "."]);
            }
        }
        $rules = array(sprintf('deleted = 0 AND organisation_id = %s', $id));
        $this->view->users = Users::find($rules);

        $logs = $this->mongoLogger->readLog(
            array('conditions' => array(
                'action' => 'login',
                'organisation_id' => $id
            ))
        );

        $countries = Countries::find(array("order" => "name"));
        $this->view->countries = Convert::toIdArray($countries);
        $organisationTypes = OrganisationTypes::find(array("order" => "name"));
        $this->view->organisationTypes = Convert::toIdArray($organisationTypes);
        $this->view->active = array($this->t->make('No'),$this->t->make('Yes'));
        $this->view->organisation = $organisation;
        $this->view->supplierInfo = $supplierInfo;
        $this->view->logs = $logs;
    }

    public function deleteAction($id){

        $organisation = Organisations::findFirst($id);

        if($organisation) {

            $organisation->softDelete();
            $transString = Translations::make("Organisation has been deleted");
            $this->session->set('message', array('type' => 'success', 'content' => $transString . '.'));
        }
        else {
            $transString = Translations::make("Organisation doesn't exist");
            $this->session->set('message', array('type' => 'warning', 'content' => $transString . '.'));
        }
        $this->response->redirect(sprintf('/%s/organisation/',$this->currentUser->Organisation->OrganisationType->getSlug()));
        $this->view->disable();
        return;
    }

    public function deactivateAction($id){

        /** @var Organisations $organisation */
        $organisation = Organisations::findFirst($id);

        if($organisation) {

            $organisation->deactivate();
            $transString = Translations::make("Organisation has been deactivated");
            $this->session->set('message', array('type' => 'success', 'content' => $transString . '.'));
        }
        else {
            $transString = Translations::make("Organisation doesn't exist");
            $this->session->set('message', array('type' => 'warning', 'content' => $transString . '.'));
        }
        $this->response->redirect(sprintf('/%s/organisation/',$this->currentUser->Organisation->OrganisationType->getSlug()));
        $this->view->disable();
        return;
    }

    public function activateAction($id)
    {
        /** @var Organisations $organisation */
        $organisation = Organisations::findFirst($id);

        if($organisation) {

            $organisation->activate();
            $transString = Translations::make("Organisation has been activated");
            $this->session->set('message', array('type' => 'success', 'content' => $transString . '.'));
        }
        else {
            $transString = Translations::make("Organisation doesn't exist");
            $this->session->set('message', array('type' => 'warning', 'content' => $transString . '.'));
        }
        $this->response->redirect(sprintf('/%s/organisation/',$this->currentUser->Organisation->OrganisationType->getSlug()));
        $this->view->disable();
        return;
    }

    public function deleteimageeditAction($id){

        $organisation = Organisations::findFirst($id);
        $file = $organisation->getLogo();
        $organisation->setLogo(null);
        $organisation->save();
        $imgDir = $this->config->application->organisationImagesDir;
        $deleted = unlink($imgDir.$file);

        return $this->response->redirect($this->request->getHTTPReferer());
    }
}
