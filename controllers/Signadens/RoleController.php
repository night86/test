<?php

namespace Signa\Controllers\Signadens;

use Signa\Models\OrganisationTypes;
use Signa\Models\RoleTemplates;
use Signa\Models\Roles;
use Signa\Libs\Convert;

class RoleController extends InitController
{
    public function indexAction(){

        $this->view->roles = RoleTemplates::find(array('deleted = 0'));
        $this->view->active = array($this->t->make('No'),$this->t->make('Yes'));
    }

    public function addAction(){

        if ($this->request->isPost()){

            $role = new RoleTemplates();
            $role->setName($this->request->getPost('name'));
            $role->setDescription($this->request->getPost('description'));
            $role->setActive($this->request->getPost('active'));
            $role->setOrganisationTypeId($this->request->getPost('organisation_type_id'));
            $role->setIsAdmin($this->request->getPost('is_admin'));
            $roles = $this->request->getPost('roles');

            if ($role->save() !== false) {

                if($roles){
                    $role->assignRoles($roles);
                }

                $this->session->set('message', ['type' => 'success', 'content' => 'New role has been added.']);
                $this->response->redirect(sprintf('/%s/role/',$this->currentUser->Organisation->OrganisationType->getSlug()));
                $this->view->disable();
                return;
            }
            else {
                $this->session->set('message', ['type' => 'error', 'content' => "Role can't be added."]);
            }
        }
        $organisation_types = OrganisationTypes::find(array("order" => "name"));
        $this->view->organisation_type = Convert::toIdArray($organisation_types);
        $this->view->roles = Roles::find();
        $this->view->active = array($this->t->make('No'),$this->t->make('Yes'));
        $this->view->is_admin = array($this->t->make('No'),$this->t->make('Yes'));
    }

    public function editAction($id){

        if ($this->request->isPost()){

            $role = RoleTemplates::findFirst($this->request->getPost('id'));
            $role->setName($this->request->getPost('name'));
            $role->setDescription($this->request->getPost('description'));
            $role->setActive($this->request->getPost('active'));
            $role->setOrganisationTypeId($this->request->getPost('organisation_type_id'));
            $role->setIsAdmin($this->request->getPost('is_admin'));
            $roles = $this->request->getPost('roles');

            if(is_null($roles)){
                $roles = array();
            }

            if ($role->save() !== false) {

                $role->assignRoles($roles);
                $role->resetRoles();
                $this->session->set('message', ['type' => 'success', 'content' => 'Role has been edited.']);
                $this->response->redirect(sprintf('/%s/role/',$this->currentUser->Organisation->OrganisationType->getSlug()));
                $this->view->disable();
                return;
            }
            else {
                $this->session->set('message', ['type' => 'error', 'content' => "Role can't be edited."]);
                return;
            }
        }

        $roleTemplate = RoleTemplates::findFirst($id);
        $roleTemplateRolesArr = array();

        foreach ($roleTemplate->roleTemplateRoles as $roleTemplateRole){

            $roleTemplateRolesArr[] = $roleTemplateRole->getRoleId();
        }

        $organisation_types = OrganisationTypes::find(array("order" => "name"));
        $this->view->organisation_type = Convert::toIdArray($organisation_types);
        $this->view->role = $roleTemplate;
        $this->view->roleRoles = $roleTemplateRolesArr;
        $this->view->roles = Roles::find();
        $this->view->active = array($this->t->make('No'),$this->t->make('Yes'));
        $this->view->is_admin = array($this->t->make('No'),$this->t->make('Yes'));
    }

    public function deleteAction($id){

        $role = RoleTemplates::findFirst($id);

        if($role) {
            $role->softDelete();
            $this->session->set('message', ['type' => 'success', 'content' => 'Role has been deleted.']);
        }
        else {
            $this->session->set('message', ['type' => 'error', 'content' => "Role is allready deleted."]);
        }
        $this->response->redirect(sprintf('/%s/role/',$this->currentUser->Organisation->OrganisationType->getSlug()));
        $this->view->disable();
        return;
    }

    public function deactivateAction($id){

        $role = RoleTemplates::findFirst($id);

        if($role) {
            $role->deactivate();
            $this->session->set('message', ['type' => 'success', 'content' => 'Role has been deactivated.']);
        }
        else {
            $this->session->set('message', ['type' => 'error', 'content' => "Role is allready deactivated."]);
        }
        $this->response->redirect(sprintf('/%s/role/',$this->currentUser->Organisation->OrganisationType->getSlug()));
        $this->view->disable();
        return;
    }

    public function activateAction($id){

        $role = RoleTemplates::findFirst($id);

        if($role) {
            $role->activate();
            $this->session->set('message', ['type' => 'success', 'content' => 'Role has been activated.']);
        }
        else {
            $this->session->set('message', ['type' => 'error', 'content' => "Role is allready activated."]);
        }
        $this->response->redirect(sprintf('/%s/role/',$this->currentUser->Organisation->OrganisationType->getSlug()));
        $this->view->disable();
        return;
    }

    public function resetAction($id){

        $role = RoleTemplates::findFirst($id);
        $reseted = $role->resetRoles();

        if($reseted) {
            $this->session->set('message', ['type' => 'success', 'content' => 'Role has been resetted.']);
        }
        else {
            $this->session->set('message', ['type' => 'error', 'content' => "Role can't be resetted."]);
        }
        $this->response->redirect(sprintf('/%s/role/edit/'.$id,$this->currentUser->Organisation->OrganisationType->getSlug()));
        $this->view->disable();
        return;
    }
}
