<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 15.07.2016
 * Time: 12:13
 */

namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\Date;
use Phalcon\Mvc\Model\Query;
use Signa\Models\UserRoles;
use Signa\Models\RoleTemplatesRoles;

class RoleTemplates extends Model
{
    protected $id;
    protected $name;
    protected $description;
    protected $active;
    protected $deleted;
    protected $organisation_type_id;
    protected $created_at;
    protected $created_by;
    protected $updated_at;
    protected $updated_by;
    protected $deleted_at;
    protected $deleted_by;
    protected $is_admin;

    public function initialize()
    {
        $this->hasMany('id', 'Signa\Models\RoleTemplatesRoles', 'role_template_id', array('alias' => 'roleTemplateRoles'));
        $this->hasMany('id', 'Signa\Models\Users', 'role_template_id', array('alias' => 'users'));
        $this->belongsTo('organisation_type_id', 'Signa\Models\OrganisationTypes', 'id', array('alias' => 'organisationType'));
    }

    public function assignRoles(array $roles)
    {
        // First roles must be deleted, then could be added
        $roleTemplateRoles = new RoleTemplatesRoles();
        $sql = "DELETE FROM `role_templates_roles` WHERE role_template_id = ".$this->getId();
        $roleTemplateRoles->getReadConnection()->query($sql);
        foreach ($roles as $role)
        {
            $roleTemplateRole = new RoleTemplatesRoles();
            $roleTemplateRole->setRoleId($role);
            $roleTemplateRole->setRoleTemplateId($this->getId());
            $roleTemplateRole->create();
        }
    }

    public function countUsers()
    {
        $counter = 0;
        foreach ($this->organisationType->organisations as $organisation)
        {
            $counter += count($organisation->users);
        }
        return $counter;
    }

    public function deactivate()
    {
        $this->setActive(0);
        $this->save();
    }

    public function activate()
    {
        $this->setActive(1);
        $this->save();
    }

    public function resetRoles()
    {
        // Getting all users with role template
        $users = $this->users;
        if(!$users){
            return false;
        }
        foreach($users as $user)
        {
            // Getting all roles for every user and remove roles
            $userRoles = UserRoles::find('user_id = '.$user->getId());
            foreach ($userRoles as $userRole)
            {
                $sql = "DELETE FROM `user_roles` WHERE user_id = ".$user->getId();
                $userRole->getReadConnection()->query($sql);
            }
        }
        // Setting roles to users
        foreach ($users as $user)
        {
            foreach ($this->roleTemplateRoles as $roleTemplateRole)
            {
                $userRole = new UserRoles();
                $userRole->setRoleId($roleTemplateRole->getRoleId());
                $userRole->setUserId($user->getId());
                $userRole->create();
            }
        }
        return true;
    }

    public function softDelete()
    {
        $user = $this->getDI()->getSession()->get('auth');
        $this->setDeletedAt(Date::currentDatetime());
        $this->setDeletedBy($user->getId());
        $this->setDeleted(1);
        $this->save();
    }

    public function beforeCreate()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setCreatedAt(Date::currentDatetime());
        $this->setCreatedBy($user->getId());
    }

    public function beforeUpdate()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setUpdatedAt(Date::currentDatetime());
        $this->setUpdatedBy(($user) ? $user->getId() : NULL);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getOrganisationTypeId()
    {
        return $this->organisation_type_id;
    }

    /**
     * @param mixed $organisation_type_id
     */
    public function setOrganisationTypeId($organisation_type_id)
    {
        $this->organisation_type_id = $organisation_type_id;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * @param mixed $created_by
     */
    public function setCreatedBy($created_by)
    {
        $this->created_by = $created_by;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
    }

    /**
     * @param mixed $updated_by
     */
    public function setUpdatedBy($updated_by)
    {
        $this->updated_by = $updated_by;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    /**
     * @param mixed $deleted_at
     */
    public function setDeletedAt($deleted_at)
    {
        $this->deleted_at = $deleted_at;
    }

    /**
     * @return mixed
     */
    public function getDeletedBy()
    {
        return $this->deleted_by;
    }

    /**
     * @param mixed $deleted_by
     */
    public function setDeletedBy($deleted_by)
    {
        $this->deleted_by = $deleted_by;
    }

    /**
     * @return mixed
     */
    public function getIsAdmin()
    {
        return $this->is_admin;
    }

    /**
     * @param mixed $is_admin
     */
    public function setIsAdmin($is_admin)
    {
        $this->is_admin = $is_admin;
    }

}