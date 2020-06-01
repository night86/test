<?php

namespace Signa\Controllers;

use Signa\Libs\Assets;
use Signa\Models\Users;

class ControllerBase extends \Phalcon\Mvc\Controller
{
    public $currentUser;
    public $baseUrl;

    public function initialize(){

        #
        # assign a refernce to the controller
        #
        $this->view->controller = $this;
        $this->view->cookieAdmin = $this->cookies->has('admin');
        $this->currentUser = $this->session->get('auth');
        $this->baseUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'];
        $this->checkKeepLoginIn($this->currentUser);

        if ($this->currentUser) {

            $organisation = (is_null($this->currentUser->Organisation))? '' : $this->currentUser->Organisation->OrganisationType->getSlug();
            $this->view->setVar('organisationSlug', $organisation );
            $this->view->currentOrganisation = $this->currentUser->Organisation;
        }
        $this->view->setVar('currentUser', $this->currentUser);
        $this->installAssets();
    }

    private function checkKeepLoginIn($user){

        $remember = $this->cookies->get('rememberMe');

        if (is_null($user) && !is_null($remember->getValue())) {

            $user = Users::findFirst($remember->getValue());
            $userRoles = array();

            foreach ($user->roles as $role) {

                $userRoles[] = $role->role->name;
            }
            $this->session->set('auth', $user);
            $this->session->set('roles', $userRoles);
        }
    }

    private function installAssets(){

        $assets = new Assets();
        $assets->installAssets();
    }

}
