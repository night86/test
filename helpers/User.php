<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 26.07.2016
 * Time: 16:40
 */

namespace Signa\Helpers;

use \Phalcon\Http\Response;
use \Phalcon\Http\Response\Cookies;
use \Phalcon\Session\Adapter\Files as Session;
use Signa\Models\Organisations;
use Signa\Models\Users;

class User
{
    /**
     * @var Session
     */
    private $session = null;

    /**
     * @var Cookies
     */
    private $cookies = null;


    /**
     * User constructor.
     * @param Session $session
     * @param Cookies $cookies
     */
    public function __construct($session, $cookies)
    {
        $this->session = $session;
        $this->cookies = $cookies;
    }

    public function logInAsUser(Users $user, Users $currentUser)
    {
        // Add info in cookies about admin to allow return button
        $this->cookies->set('admin', $currentUser->getId(), time() + 240 * 86400);
        $this->changeUserSession($user, false);
    }

    public function logInByMasterkey(Users $user, Users $currentUser)
    {
        // Add info in cookies about admin to allow return button
        $this->cookies->set('masterkey', $currentUser->getId(), time() + 15 * 86400);
        $this->changeMasterkeySession($user, false);
    }

    /**
     * return to admin user
     *
     * @throws Exception
     */
    public function backToAdminUser()
    {
        if ((!$this->cookies->has('admin') && !$this->cookies->has('masterkey')) ||
            (!$this->session->has("auth_back") && !$this->session->has("auth_back_masterkey"))) {

            return false;
        }

        if ($this->session->has("auth_back_masterkey")) {
            $adminUserCookie = Users::findFirstById($this->cookies->get('masterkey'));
            $adminUser = $this->session->get("auth_back_masterkey");

            if ($adminUser->getId() != $adminUserCookie->getId()) {
                return false;
            }
            // remove cookie with current admin user id
            $this->cookies->get('masterkey')->delete();

            $this->changeMasterkeySession($adminUserCookie, true);

            return $adminUser;
        }
        else {
            $adminUserCookie = Users::findFirstById($this->cookies->get('admin'));
            $adminUser = $this->session->get("auth_back");

            if ($adminUser->getId() != $adminUserCookie->getId()) {
                return false;
            }
            // remove cookie with current admin user id
            $this->cookies->get('admin')->delete();
            $this->changeUserSession($adminUserCookie, true);

            return $adminUser;
        }
    }

    private function changeMasterkeySession(Users $user, $backToAdmin)
    {
        $this->session->destroy();
        $this->session->start();
//        session_abort();
//        session_start(['read_and_close' => true]);
        $userRoles = array();
        foreach ($user->roles as $role){
            $userRoles[] = $role->role->name;
        }

        $this->session->set('auth', $user);
        $this->session->set('roles', $userRoles);

        // Add info in session about masterkey
        if(!$backToAdmin && $this->cookies->has('masterkey')) {
            $this->session->set('auth_back_masterkey', Users::findFirstById($this->cookies->get('masterkey')));
        }

        // Add info in session about admin
        if($this->cookies->has('admin')) {
            $this->session->set('auth_back', Users::findFirstById($this->cookies->get('admin')));
        }
    }

    private function changeUserSession(Users $user, $backToAdmin)
    {
        $this->session->destroy();
        $this->session->start();
//        session_abort();
//        session_start(['read_and_close' => true]);
        $userRoles = array();
        foreach ($user->roles as $role){
            $userRoles[] = $role->role->name;
        }

        $this->session->set('auth', $user);
        $this->session->set('roles', $userRoles);

        // Add info in session about admin
        if(!$backToAdmin && $this->cookies->has('admin')) {
            $this->session->set('auth_back', Users::findFirstById($this->cookies->get('admin')));
        }
    }

    public static function getSignaOrganisationIds()
    {
        $signaOrganisationTypeId = 2;
        $organisationIdsArr = array();
        foreach(Organisations::find(array('organisation_type_id = '.$signaOrganisationTypeId, 'columns' => 'id')) as $organisation)
        {
            $organisationIdsArr[] = $organisation['id'];
        }
        return $organisationIdsArr;
    }
}