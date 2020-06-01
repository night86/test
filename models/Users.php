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
use Signa\Models\RoleTemplatesRoles;

class Users extends Model
{
    protected $id;
    protected $email;
    protected $password;
    protected $last_login;
    protected $firstname;
    protected $lastname;
    protected $address;
    protected $zip_code;
    protected $city;
    protected $country;
    protected $telephone;
    protected $locations;
    protected $active;
    protected $deleted;
    protected $organisation_id;
    protected $role_template_id;
    protected $main_location_id;
    protected $start_page;
    protected $created_at;
    protected $created_by;
    protected $updated_at;
    protected $updated_by;
    protected $deleted_at;
    protected $deleted_by;
    protected $role_template_id_old;

    public function initialize()
    {
        $this->belongsTo('organisation_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Organisation'));
        $this->belongsTo('main_location_id', 'Signa\Models\DentistLocation', 'id', array('alias' => 'MainLocation'));
        $this->belongsTo('role_template_id', 'Signa\Models\RoleTemplates', 'id', array('alias' => 'roleTemplate'));
        $this->hasOne('id', 'Signa\Models\UserDepartments()', 'user_id', array('alias' => 'UserDepartments'));

        $this->hasMany('id', 'Signa\Models\UserRoles', 'user_id', array('alias' => 'UserRoles'));
        $this->hasMany('id', 'Signa\Models\SettingFiles', 'to_user_id', array('alias' => 'SettingFiles'));
        $this->hasManyToMany(
            "id",
            "Signa\Models\ProjectUsers",
            "user_id",
            "project_id",
            "Signa\Models\Projects",
            "id",
            array('alias' => 'Projects')
        );

        $this->hasManyToMany(
            "organisation_id",
            Organisations::class,
            'id',
            'organisation_type_id',
            OrganisationTypes::class,
            'id',
            array('alias' => 'UserSlug')

        );
    }

    public function getName() {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function copyRoles()
    {
        $sql = "DELETE FROM `user_roles` WHERE user_id = ".$this->id;
        $this->getReadConnection()->query($sql);

        $roles = RoleTemplatesRoles::findByRoleTemplateId($this->roleTemplate->getId());

        foreach ($roles as $role)
        {
            $userRole = new UserRoles();
            $userRole->setRoleId($role->getRoleId());
            $userRole->setUserId($this->id);
            $userRole->create();
        }
    }

    /**
     * @return array
     */
    public function getRolesArray()
    {
        $roles = array();
        foreach($this->UserRoles as $roleObj) {
            if ($roleObj->Role) {
                $roles[] = $roleObj->Role->getName();
            }
        }

        return $roles;
    }

    /**
     * check if use has role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        $result = false;
        foreach($this->UserRoles as $roleObj) {
            if ($roleObj->Role && $roleObj->Role->getName() == $role) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    public function hasFiles()
    {
        $files = Files::find('created_by = '.$this->getId());
        $connectedFiles = FileSharedUser::find('user_id = '.$this->getId().' AND status = 1');
        $sum = count($files) + count($connectedFiles);
        return (bool)$sum;
    }

    public function getDepartment()
    {
        $userDepartment = UserDepartments::findFirst('user_id = '.$this->getId());
        if(!$userDepartment) return null;
        $department = Departments::findFirst('id = '.$userDepartment->getDepartmentId());
        return $department;
    }
    public function getDepartmentName()
    {
        $userDepartment = UserDepartments::findFirst('user_id = '.$this->getId());
        if(!$userDepartment) return null;
        $department = Departments::findFirst('id = '.$userDepartment->getDepartmentId());
        return $department->getName();
    }
    public function setUserDepartment($departmentId)
    {
        $userDepartment = UserDepartments::findFirst($this->getId());
        if(!$userDepartment) {
            $userDepartment = new UserDepartments();
        }
        $userDepartment->setUserId($this->getId());
        $userDepartment->setDepartmentId($departmentId);
        $userDepartment->save();
        return;

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

    public function updateLastLogin()
    {
        $this->setLastLogin(date('Y-m-d H:i:s'));
        return $this->save();
    }

    public function getDateTimeArr()
    {
        return Date::dateTimeToArr($this->getLastLogin());
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
        if ($user) {
            $this->setCreatedBy($user->getId());
        }
    }

    public function beforeUpdate()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setUpdatedAt(Date::currentDatetime());
        if($user){
            $this->setUpdatedBy($user->getId());
        }else{
            $this->setUpdatedBy(0);
        }
    }

    public function afterFetch()
    {
        $this->role_template_id_old = $this->role_template_id;
    }

    public function afterSave()
    {
        if ($this->role_template_id_old !== $this->role_template_id) {
            $this->roleTemplate->resetRoles();
            $this->role_template_id_old = $this->role_template_id;
        }
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = md5($password);
    }

    /**
     * @return mixed
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * @param mixed $last_login
     */
    public function setLastLogin($last_login)
    {
        $this->last_login = $last_login;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        if ($this->firstname || $this->lastname) {
            $fullname = sprintf('%s %s', $this->firstname, $this->lastname);
        } else {
            $fullname = $this->getEmail();
        }
        return $fullname;
    }

    /**
     * @return string
     */
    public function getFullNameWithEmail()
    {
        if ($this->firstname || $this->lastname) {
            $fullname = sprintf('%s %s (%s)', $this->firstname, $this->lastname, $this->email);
        } else {
            $fullname = $this->getEmail();
        }
        return $fullname;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zip_code;
    }

    /**
     * @param mixed $zip_code
     */
    public function setZipCode($zip_code)
    {
        $this->zip_code = $zip_code;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getLocations()
    {
        return json_decode($this->locations, true);
    }

    /**
     * @param mixed $locations
     */
    public function setLocations($locations)
    {
        $this->locations = json_encode($locations);
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
    public function getOrganisationId()
    {
        return $this->organisation_id;
    }

    /**
     * @param mixed $organisation_id
     */
    public function setOrganisationId($organisation_id)
    {
        $this->organisation_id = $organisation_id;
    }

    /**
     * @return mixed
     */
    public function getMainLocationId()
    {
        return $this->main_location_id;
    }

    /**
     * @param mixed $main_location_id
     */
    public function setMainLocationId($main_location_id)
    {
        $this->main_location_id = $main_location_id;
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
    public function getStartPage()
    {
        // we dont want go to start page when user is MASTERKEY
        if ($this->hasRole('ROLE_LAB_USER_MASTERKEY')) {
            return 0;
        }
        return $this->start_page;
    }

    /**
     * @param mixed $start_page
     */
    public function setStartPage($start_page)
    {
        $this->start_page = $start_page;
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
}