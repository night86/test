<?php

namespace Signa\Controllers\Supplier;

use Signa\Models\Users;
use Signa\Models\Organisations;
use Signa\Models\RoleTemplates;
use Signa\Helpers\User as UserHelper;
use Signa\Helpers\Translations as Trans;
use Signa\Helpers\General;

class UserController extends \Signa\Controllers\Signadens\UserController
{
    public function indexAction(){

        $this->view->organisation = Organisations::findFirst($this->currentUser->getOrganisationId());
        $this->view->users = Users::find('deleted = 0 AND organisation_id = '.$this->currentUser->getOrganisationId());
        $this->view->active = array($this->t->make('No'),$this->t->make('Yes'));
    }

    public function addAction(){

        parent::addAction();

        $this->view->organisation = $this->currentUser->Organisation;
        $this->view->roles = RoleTemplates::find('deleted_at IS NULL AND organisation_type_id = '.$this->currentUser->Organisation->getOrganisationTypeId());
    }

    public function editAction($id){

        parent::editAction($id);

        $this->view->organisation = $this->currentUser->Organisation;
        $this->view->roles = RoleTemplates::find('deleted_at IS NULL AND organisation_type_id = '.$this->currentUser->Organisation->getOrganisationTypeId());
    }

    public function organisationAction($id){

        $organisation = Organisations::findFirst($id);

        if ($this->request->isPost()){

            if ($this->request->hasFiles() == true){

                $imgDir = $this->config->application->organisationImagesDir;
                $randomString = General::randomString(6);
                $file = $this->request->getUploadedFiles()[0];
                $fileName = $randomString.$file->getName();

                if($file->getRealType() == 'image/jpeg' || $file->getRealType() == 'image/png'){

                    if(!is_dir($imgDir)) {
                        mkdirR($imgDir);
                    }
                    $file->moveTo($imgDir.$fileName);
                    $organisation->setLogo($fileName);
                }
            }
            else {
                $this->session->set('message', ['type' => 'error', 'content' => Trans::make('Organisation can\'t be edited.')]);
            }

            if ($organisation->save() !== false) {

                $this->session->set('message', ['type' => 'success', 'content' => Trans::make('Organisation has been edited.')]);
                $this->response->redirect('supplier/user/organisation/'.$id);
                $this->view->disable();
                return;
            }
            else {
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
}
