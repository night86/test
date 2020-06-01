<?php

namespace Signa\Controllers\Lab;

use Signa\Models\Departments;
use Signa\Models\Users;
use Signa\Models\Organisations;
use Signa\Models\RoleTemplates;
use Signa\Models\LabPaymentArrangements;
use Signa\Helpers\User as UserHelper;
use Signa\Helpers\Translations as Trans;
use Signa\Helpers\General;

class UserController extends \Signa\Controllers\Signadens\UserController
{
    public function indexAction(){

        $this->view->organisation = Organisations::findFirst($this->currentUser->getOrganisationId());;
        $this->view->users = Users::find('deleted = 0 AND organisation_id = '.$this->currentUser->getOrganisationId());
        $this->view->active = array($this->t->make('No'),$this->t->make('Yes'));
    }

    public function addAction(){

        parent::addAction();

        $departmentId = is_null($this->currentUser->getDepartment()) ? null : $this->currentUser->getDepartment();
        $this->view->department = Departments::findFirst(['id' => $departmentId]);
        $this->view->organisation = $this->currentUser->Organisation;
        $this->view->roles = RoleTemplates::find('deleted_at IS NULL AND organisation_type_id = '.$this->currentUser->Organisation->getOrganisationTypeId());
    }

    public function editAction($id){

        parent::editAction($id);

        $department = is_null($this->currentUser->getDepartment()) ? null : $this->currentUser->getDepartment();
        $this->view->organisation = $this->currentUser->Organisation;
        $this->view->roles = RoleTemplates::find('deleted_at IS NULL AND organisation_type_id = '.$this->currentUser->Organisation->getOrganisationTypeId());

    }

    public function masterkeyAction(){

        $this->view->users = Users::find('deleted = 0 AND active = 1 AND organisation_id = '.$this->currentUser->getOrganisationId());
        $this->view->active = array($this->t->make('No'),$this->t->make('Yes'));
        $this->view->isHeader = 'loginout';
        $this->view->disableSubnav = true;
    }

    public function organisationAction($id)
    {
        $organisation = Organisations::findFirst($id);

        if ($this->request->isPost())
        {
            if ($this->request->hasFiles() == true) {
                $imgDir = $this->config->application->organisationImagesDir;
                $randomString = General::randomString(6);
                $file = $this->request->getUploadedFiles()[0];
                $fileName = $randomString.$file->getName();

                if($file->getRealType() == 'image/jpeg' || $file->getRealType() == 'image/png')
                {

                    if(!is_dir($imgDir)) {
                        mkdirR($imgDir);
                    }
                    $file->moveTo($imgDir.$fileName);
                    $organisation->setLogo($fileName);
                }
            } else {
                $this->session->set('message', ['type' => 'error', 'content' => Trans::make('Organisation can\'t be edited.')]);
            }

            if ($organisation->save() !== false) {
                $this->session->set('message', ['type' => 'success', 'content' => Trans::make('Organisation has been edited.')]);
                $this->response->redirect('lab/user/organisation/'.$id);
                $this->view->disable();
                return;
            }else{
                $this->session->set('message', ['type' => 'error', 'content' => Trans::make('Organisation can\'t be edited.')]);
            }
        }

        $this->view->organisation = $organisation;
    }

    public function deleteorganisationimageAction($id){
        $organisation = Organisations::findFirst($id);
        $file = $organisation->getLogo();
        $organisation->setLogo(null);
        $organisation->save();

        $imgDir = $this->config->application->organisationImagesDir;

        $deleted = unlink($imgDir.$file);

        return $this->response->redirect($this->request->getHTTPReferer());
    }

    /**
     * login as user without password - only for masterkey account
     */
    public function loginbymasterkeyAction($userId)
    {
        $user = Users::findFirst($userId);
        if (!$user) {
            throw new \Exception('User not exist');
        }

        $userHelper = new UserHelper($this->session, $this->cookies);
        $redirectUrl = '';

        try {
            $userHelper->logInByMasterkey($user, $this->currentUser);
            $redirectUrl = $this->access->getDefaultUrl();

        } catch (\Exception $e) {
            $this->view->error = $e->getMessage();
            return;
        }
        $this->response->redirect($redirectUrl);
    }

    public function ajaxpaymentoptionAction(){

        $this->view->disable();

        if ($this->request->isPost()) {

            $post = $this->request->getPost();

            if($post['id'] !== ""){

                $editPayment = LabPaymentArrangements::findFirst($post['id']);
                $editPayment->setCode($post['code']);
                $editPayment->setDescription($post['description']);
                $editPayment->setPercentage($post['percentage']);
                $editPayment->save();

                $result = [
                    "status"    => "ok",
                    "msg"       => Trans::make("Payment arrangement updated successfully")
                ];
            }
            else {

                $newPayment = new LabPaymentArrangements();
                $newPayment->setLabId($this->currentUser->getOrganisationId());
                $newPayment->setCode($post['code']);
                $newPayment->setDescription($post['description']);
                $newPayment->setPercentage($post['percentage']);
                $newPayment->save();

                $result = [
                    "status"    => "ok",
                    "msg"       => Trans::make("Payment arrangement added successfully")
                ];
            }
            return json_encode($result);
        }
    }

    public function deletepaymentoptionAction($id){

        $this->view->disable();
        $deletePayment = LabPaymentArrangements::findFirst($id);
        $deletePayment->softDelete();
        return $this->response->redirect('/general/organisationEdit/'.$this->currentUser->getOrganisationId());
    }
}
