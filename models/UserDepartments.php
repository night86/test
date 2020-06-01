<?php
namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\Date;
use Signa\Models\RoleTemplatesRoles;

class UserDepartments extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $user_id;

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $department_id;

    /**
     * Method to set the value of field user_id
     *
     * @param integer $user_id
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }
    public function initialize()
    {
        $this->belongsTo('department_id', 'Signa\Models\Departments', 'id', array('alias' => 'Department'));
        $this->belongsTo('user_id', 'Signa\Models\Users', 'id', array('alias' => 'User'));
    }
    /**
     * Method to set the value of field department_id
     *
     * @param integer $department_id
     * @return $this
     */
    public function setDepartmentId($department_id)
    {
        $this->department_id = $department_id;

        return $this;
    }

    /**
     * Returns the value of field user_id
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Returns the value of field department_id
     *
     * @return integer
     */
    public function getDepartmentId()
    {
        return $this->department_id;
    }


    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'user_departments';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return UserDepartments[]|UserDepartments
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return UserDepartments
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
