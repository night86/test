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
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;

class Manufacturers extends Model
{
    protected $id;
    protected $name;
    protected $created_by;
    protected $created_at;
    protected $updated_by;
    protected $updated_at;
    protected $deleted_by;
    protected $deleted_at;

    public function initialize()
    {
//        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
//        $this->belongsTo('updated_by', 'Signa\Models\Users', 'id', array('alias' => 'UpdatedBy'));
//        $this->belongsTo('product_id', 'Signa\Models\Products', 'id', array('alias' => 'Product'));
//        $this->belongsTo('organisation_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Organisation'));
//        $this->setSource("code_ledgers");
    }

    public function validation()
    {

    }

    public function setDatas($data)
    {

    }

    public function activateDeactivate($status)
    {

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
        $this->setUpdatedBy($user->getId());
    }

    public function beforeDelete()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setDeletedAt(Date::currentDatetime());
        $this->setDeletedBy($user->getId());
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
}