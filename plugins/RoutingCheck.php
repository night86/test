<?php

use Phalcon\Events\Event,
        Phalcon\Mvc\User\Plugin,
        Phalcon\Mvc\Dispatcher,
        Phalcon\Acl;

use Signa\Models\User;

class RoutingCheck extends Plugin
{
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        $auth = $this->session->get('auth');
        if (!$auth) {
            $roles = ['ROLE_GUEST'];
        } else {
            $roles = $auth->getRolesArray();
            $roles[] = 'ROLE_GUEST'; // default role for all users
        }
//\dump($auth); die;
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();
        $namespace = $dispatcher->getNamespaceName();

        // get organisation slug if exist
        $uri = $this->router->getRewriteUri();
        $uriArr = explode(strtolower($controller), $uri);
        $organisation = str_replace('/', '', $uriArr[0]);
        if ($organisation == '') {
            $organisation = 'guestauth'; // guest access for pages without organisation
        }

        $acl = $this->getDI()->get('acl');

        $allowed = Acl::DENY;
        foreach ($roles as $role) {

            $param = array($organisation);
            if (!$param || empty($param)) {
                $param = array('default');
            }
            error_reporting(0);
//            echo "$role | $organisation | $controller | $action | $param"; die;
            $allowed = $acl->isAllowed($role, $organisation.$controller, strtolower($action), $param);
            if (in_array($_SERVER['HTTP_HOST'], array('localhost', 'signadens.dev', 'signadens.devv'))) {
                error_reporting(E_ALL);
            }
            if ($allowed == Acl::ALLOW) {
                break;
            }
        }

        if ($allowed != Acl::ALLOW) {

            if (!$auth) {
                $dispatcher->setReturnedValue($this->response->redirect('auth/login'));
//                return false;
            } else {
                $this->session->set('message', array('type' => 'warning', 'content' => 'You don\'t have access.'));
//                echo $auth->Organisation->OrganisationType->getSlug(); die;
                $dispatcher->setReturnedValue($this->response->redirect($auth->Organisation->OrganisationType->getSlug().'/index/'));
            }

            // Returning "false" we tell to the dispatcher to stop the current operation
            return false;
        }

    }
}