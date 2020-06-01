<?php

namespace Signa\Libs;

use Phalcon\Events\Event,
        Phalcon\Mvc\User\Plugin,
        Phalcon\Mvc\Dispatcher,
        Phalcon\Acl;

class Access extends Plugin
{
    /**
     * @param string|null $organisation
     * @param string $controller
     * @param string $action
     * @return bool
     */
    public function hasAccess($organisation, $controller, $action)
    {
        $auth = $this->session->get('auth');
        if (!$auth) {
            $roles = ['ROLE_GUEST'];
        } else {
            $roles = $auth->getRolesArray();
            $roles[] = 'ROLE_GUEST'; // default role for all users
        }

        // manual access for admin
        if (in_array('ROLE_ADMIN', $roles)) {
            return true;
        }

        $acl = $this->getDI()->get('acl');

        $allowed = Acl::DENY;
        foreach ($roles as $role) {
            $allowed = $acl->isAllowed($role, $organisation.$controller, strtolower($action), array($organisation));
            if ($allowed == Acl::ALLOW) {
                break;
            }
        }

        if ($allowed == Acl::ALLOW) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get current default url based on user organisation
     * @return string
     */
    public function getDefaultUrl() {

        $auth = $this->session->get('auth');
        $url = '/';
        if ($auth) {
            // if you are masterkey you should be redirect to masterkey view
            if ($auth->hasRole('ROLE_LAB_USER_MASTERKEY')) {
                $url = sprintf('%s/user/masterkey', $auth->Organisation->OrganisationType->getSlug());
            } else {
                $url = sprintf('%s/index/', $auth->Organisation->OrganisationType->getSlug());
            }
        }

        return $url;
    }
}