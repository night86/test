<?php

namespace Signa\Controllers\Signadens;

use Signa\Helpers\Translations;
use Signa\Models\Departments;
use Signa\Models\Logs;
use Signa\Models\Users;
use Signa\Models\Organisations;
use Signa\Models\RoleTemplates;
use Signa\Helpers\User as UserHelper;

use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

class UserController extends InitController
{
    public function indexAction(){

        $this->assets->collection('footer')
            ->addJs("js/app/user.js");

        $this->view->organisations = Organisations::find('deleted_at IS NULL');
        $this->view->roles = RoleTemplates::find(array('deleted = 0 AND active = 1'));
    }

    public function addAction(){

        $passwd = $this->generatePasswd();

        if ($this->request->isPost()) {

            if (!$this->request->hasPost('organisation_id')) {

                $organisationId = $this->currentUser->Organisation->getId();
            }
            else {
                $organisationId = $this->request->getPost('organisation_id');
            }

            $user = new Users();
            $user->setEmail($this->request->getPost('email'));
            $user->setPassword($passwd);
            $user->setFirstname($this->request->getPost('firstname'));
            $user->setLastname($this->request->getPost('lastname'));
            $user->setTelephone($this->request->getPost('telephone'));
            $user->setOrganisationId($organisationId);
            $user->setRoleTemplateId($this->request->getPost('role_template_id'));
            $user->setActive($this->request->getPost('active'));

            $params = array("email" => $user->getEmail(), "password" => $passwd, "button" => array('url' => '', 'text' => 'Login'));

            if ($user->create() !== false) {

                $sended = $this->mail->send($user->getEmail(), $this->t->make('Welcome in Signadens'), 'newUser', $params);
                $user->copyRoles();

                if (!$this->request->hasPost('department_id')) {

                    $departmentId = is_null($this->currentUser->getDepartment()) ? null : $this->currentUser->getDepartment()->getId();
                }
                else {
                    $departmentId = $this->request->getPost('department_id');
                }
                $user->setUserDepartment($this->request->getPost($departmentId));
                $message = [
                    'type' => 'success',
                    'content' => 'New user has been added.'
                ];
                $this->session->set('message', $message);
                $this->response->redirect(sprintf('/%s/user/', $this->currentUser->Organisation->OrganisationType->getSlug()));
                $this->view->disable();
                return;
            }
            else {
                $message = [
                    'type' => 'error',
                    'content' => 'User can\'t be added.'
                ];
                $this->session->set('message', $message);
            }
        }
        $this->view->departments = Departments::find();
        $this->view->organisations = Organisations::find();
        $this->view->roles = RoleTemplates::find();
        $this->view->active = array($this->t->make('No'), $this->t->make('Yes'));
        $this->assets->collection('footer')
            ->addJs("js/app/useradd.js");
    }

    public function editAction($id){

        $user = Users::findFirst($id);

        if ($this->currentUser->Organisation->OrganisationType->getSlug() != 'signadens'
            && $user->Organisation->OrganisationType->getId() != $this->currentUser->Organisation->OrganisationType->getId()
        ) {
            $this->response->redirect(sprintf('/%s/user/', $this->currentUser->Organisation->OrganisationType->getSlug()));
            $this->view->disable();
            return;
        }

        if ($this->request->isPost()) {

            if (!$this->request->hasPost('organisation_id')) {

                $organisationId = $this->currentUser->Organisation->getId();
            }
            else {
                $organisationId = $this->request->getPost('organisation_id');
            }

            if ($this->request->getPost('password')) {

                $user->setPassword($this->request->getPost('password'));
            }
            $user->setFirstname($this->request->getPost('firstname'));
            $user->setLastname($this->request->getPost('lastname'));
            $user->setTelephone($this->request->getPost('telephone'));
            $user->setActive($this->request->getPost('active'));
            $user->setEmail($this->request->getPost('email'));
            $user->setOrganisationId($organisationId);

            if($this->currentUser->Organisation->OrganisationType->getSlug() == 'dentist'){

                $location = $this->request->getPost('location');

                if(!empty($location) && $location != NULL){

                    $arr = [];

                    foreach ($location as $k => $v){

                        if($v == 1){
                            $arr[] = $k;
                        }
                    }
                    $user->setLocations($arr);
                }

                $user->setMainLocationId($this->request->getPost('main_location_id'));
            }
            else {
                $user->setLocations(NULL);
                $user->setMainLocationId(NULL);
            }

            $user->setRoleTemplateId($this->request->getPost('role_template_id'));

            if ($user->save() !== false) {

                if (!$this->request->hasPost('department_id')) {

                    $departmentId = (is_null($this->currentUser->getDepartment())) ? null : $this->currentUser->getDepartment()->getId();
                }
                else {
                    $departmentId = $this->request->getPost('department_id');
                }
                $user->setUserDepartment($departmentId);
                $this->session->set('message', array('type' => 'success', 'content' => 'User has been edited.'));
                $this->response->redirect(sprintf('/%s/user/', $this->currentUser->Organisation->OrganisationType->getSlug()));
                $this->view->disable();

                return;
            }
            else {
                $this->session->set('message', array('type' => 'warning', 'content' => 'User can\'t be edited.'));
                $this->response->redirect(sprintf('/%s/user/edit/' . $user->getId(), $this->currentUser->Organisation->OrganisationType->getSlug()));
                $this->view->disable();
                return;
            }
        }
        $logs = $this->mongoLogger->readLog(
            array('conditions' => array(
                'user' => $user->getEmail()
            ))
        );
        $roles = preg_grep('~' . preg_quote('ROLE_LAB', '~') . '~', $user->getRolesArray());

        if(count($roles)>0) {
            $departments = Departments::find();
            $this->view->departments=$departments;
        }
        $this->view->logs = $logs;
        $this->view->user = $user;
        $this->view->organisations = Organisations::find();
        $this->view->roles = RoleTemplates::find();
        $this->view->active = array($this->t->make('No'), $this->t->make('Yes'));
        $this->assets->collection('footer')
            ->addJs("js/app/useradd.js");
    }

    public function deleteAction($id){

        $user = Users::findFirst($id);

        if ($user) {
            $user->softDelete();
            $this->session->set('message', array('type' => 'success', 'content' => 'User has been deleted.'));
        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => 'User doesn\'t exist.'));
        }
        $this->response->redirect(sprintf('/%s/user/', $this->currentUser->Organisation->OrganisationType->getSlug()));
        $this->view->disable();
        return;
    }

    public function deactivateAction($id){

        /** @var Users $user */
        $user = Users::findFirst($id);

        if ($user) {
            $user->deactivate();
            $this->session->set('message', array('type' => 'success', 'content' => 'User has been deactivated.'));
        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => 'User doesn\'t exist.'));
        }
        $this->response->redirect(sprintf('/%s/user/', $this->currentUser->Organisation->OrganisationType->getSlug()));
        $this->view->disable();
        return;
    }

    public function activateAction($id){

        /** @var Users $user */
        $user = Users::findFirst($id);

        if ($user) {
            $user->activate();
            $this->session->set('message', array('type' => 'success', 'content' => 'User has been activated.'));
        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => 'User doesn\'t exist.'));
        }
        $this->response->redirect(sprintf('/%s/user/', $this->currentUser->Organisation->OrganisationType->getSlug()));
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

        if($admin != false){

            if ($admin->hasRole('ROLE_LAB_USER_MASTERKEY')) {

                $url = sprintf('%s/user/masterkey', $admin->Organisation->OrganisationType->getSlug());
            }
            else {
                $url = sprintf('%s/user/', $admin->Organisation->OrganisationType->getSlug());
            }
            $this->response->redirect($url);
        }
        else {
            $this->response->redirect('/');
        }
    }

    public function ajaxlistAction(){

        $users = Users::find(array('deleted = 0'));
        $active = array($this->t->make('No'), $this->t->make('Yes'));
        $dataArr = array();

        foreach ($users as $key => $user) {

            $dataArr[$key]['email'] = $user->getEmail();
            $dataArr[$key]['active'] = $active[(int)$user->getActive()];
            $dataArr[$key]['first_name'] = $user->getFirstName();
            $dataArr[$key]['last_name'] = $user->getLastName();
            $dataArr[$key]['organisation'] = $user->organisation != false ? $user->organisation->getName() : '-';
            $dataArr[$key]['role'] = $user->roleTemplate != false ? $user->roleTemplate->getName() : '-';
            $dataArr[$key]['actions'] = '<a href="/signadens/user/edit/' . $user->getId() . '" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> ' . $this->t->make('Edit') . '</a> ';

            if ($user->getActive()) {
                $dataArr[$key]['actions'] .= '<a href="/signadens/user/deactivate/' . $user->getId() . '" class="btn btn-danger btn-sm"><i class="pe-7s-close-circle"></i> ' . $this->t->make('Deactivate') . '</a> ';
            }
            else {
                $dataArr[$key]['actions'] .= '<a href="/signadens/user/activate/' . $user->getId() . '" class="btn btn-success btn-sm"><i class="pe-7s-gleam"></i> ' . $this->t->make('Activate') . '</a> ';
            }
            $dataArr[$key]['actions'] .= '<a href="/signadens/user/loginasuser/' . $user->getId() . '" class="btn btn-warning btn-sm"><i class="pe-7s-glasses"></i> ' . $this->t->make('Login as') . '</a>';
        }
        return json_encode(array('data' => $dataArr));
    }

    private function generatePasswd(){

        $generator = new ComputerPasswordGenerator();

        $generator
            ->setUppercase()
            ->setLowercase()
            ->setNumbers()
            ->setSymbols(false)
            ->setLength(12);

        return $generator->generatePassword();
    }
}
