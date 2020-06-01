<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 15.07.2016
 * Time: 12:13
 */

namespace Signa\Models;

use Phalcon\Mvc\Model;

class UserRoles extends Model
{
    protected $user_id;
    protected $role_id;

    public function initialize()
    {
        $this->belongsTo('role_id', 'Signa\Models\Roles', 'id', array('alias' => 'Role'));
        $this->belongsTo('user_id', 'Signa\Models\Users', 'id', array('alias' => 'User'));
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getRoleId()
    {
        return $this->role_id;
    }

    /**
     * @param mixed $role_id
     */
    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;
    }
}