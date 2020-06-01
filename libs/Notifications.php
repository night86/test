<?php

namespace Signa\Libs;

use Phalcon\Events\Event,
    Phalcon\Mvc\User\Plugin,
    Phalcon\Mvc\Dispatcher,
    Phalcon\Acl;

use Signa\Models\Users;
use Signa\Models\Notifications as NotificationsModel;
use Signa\Models\Roles as RolesModel;
use Signa\Models\UserRoles;

class Notifications extends Plugin
{
    /**
     * get count of unreaded notifications
     *
     * @return int
     */
    public function countUnreaded()
    {
        $user = $this->session->get('auth');
        $unreadRules = array(
            sprintf('user_id = %s AND read_at is NULL AND archived_at is NULL AND (send_at IS NULL OR send_at <= "%s")', $user->getId(), date('Y-m-d'))
        );
        $notifications = NotificationsModel::find($unreadRules);

        return count($notifications);
    }

    public function addNotification($array, $role = null, $organisationId = null, $userIdArr = null)
    {
        $user = $this->session->get('auth');

        if (is_null($organisationId) AND is_null($userIdArr)) {
            $roleId = RolesModel::findFirst(array("name = '$role'"));
            $tousers = UserRoles::find('role_id = ' . $roleId->getId());
        } elseif (is_null($role) AND is_null($userIdArr)) {
            $tousers = Users::find('organisation_id = ' . $organisationId);
        } elseif (!is_null($userIdArr)) {
            $tousers = $userIdArr;
        }

        foreach ($tousers as $touser) {
            if ($touser instanceof UserRoles) {
                if(Users::findFirst($touser->getUserId())){
                    $touserId = $touser->getUserId();
                }
            } elseif ($touser instanceof Users) {
                $touserId = $touser->getId();
            } else {
                $touserId = $touser;
            }

            if(isset($touserId) && !is_null($touserId)){
                $orgId = Users::findFirst($touserId)->getOrganisationId();

                $aditional = array(
                    'user_id' => $touserId,
                    'created_by' => $user->getId(),
                    'created_at' => date('Y-m-d H:i:s')
                );

                $array = array_merge($array, $aditional);

                $notification = new NotificationsModel();
                $notification->setOrganisationFrom($user->Organisation->getId());
                $notification->setOrganisationTo($orgId);
                $notification->saveNotification($array);
            }
        }

    }
}