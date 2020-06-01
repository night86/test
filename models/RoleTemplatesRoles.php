<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 15.07.2016
 * Time: 12:13
 */

namespace Signa\Models;

use Phalcon\Mvc\Model;

class RoleTemplatesRoles extends Model
{
    protected $role_template_id;
    protected $role_id;

    public function initialize()
    {
        $this->belongsTo('role_id', 'Signa\Models\Roles', 'id', array('alias' => 'Role'));
        $this->belongsTo('role_template_id', 'Signa\Models\RoleTemplates', 'id', array('alias' => 'Template'));
    }

    /**
     * @return mixed
     */
    public function getRoleTemplateId()
    {
        return $this->role_template_id;
    }

    /**
     * @param mixed $role_template_id
     */
    public function setRoleTemplateId($role_template_id)
    {
        $this->role_template_id = $role_template_id;
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