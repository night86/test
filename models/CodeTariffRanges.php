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

class CodeTariffRanges extends Model
{
    protected $id;
    protected $manufacturer_id;
    protected $product_category_id;
    protected $range_from;
    protected $range_to;
    protected $created_by;
    protected $created_at;
    protected $updated_by;
    protected $updated_at;
    protected $deleted_by;
    protected $deleted_at;

    public function initialize()
    {
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
        $this->belongsTo('updated_by', 'Signa\Models\Users', 'id', array('alias' => 'UpdatedBy'));
        $this->belongsTo('deleted_by', 'Signa\Models\Users', 'id', array('alias' => 'DeletedBy'));
        $this->belongsTo('manufacturer_id', 'Signa\Models\Manufacturers', 'id', array('alias' => 'Manufacturer'));
        $this->belongsTo('product_category_id', 'Signa\Models\ProductCategories', 'id', array('alias' => 'ProductCategory'));
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
    public function getManufacturerId()
    {
        return $this->manufacturer_id;
    }

    /**
     * @param mixed $manufacturer_id
     */
    public function setManufacturerId($manufacturer_id)
    {
        $this->manufacturer_id = $manufacturer_id;
    }

    /**
     * @return mixed
     */
    public function getProductCategoryId()
    {
        return $this->product_category_id;
    }

    /**
     * @param mixed $product_category_id
     */
    public function setProductCategoryId($product_category_id)
    {
        $this->product_category_id = $product_category_id;
    }

    /**
     * @return mixed
     */
    public function getRangeFrom()
    {
        return $this->range_from;
    }

    /**
     * @param mixed $range_from
     */
    public function setRangeFrom($range_from)
    {
        $this->range_from = $range_from;
    }

    /**
     * @return mixed
     */
    public function getRangeTo()
    {
        return $this->range_to;
    }

    /**
     * @param mixed $range_to
     */
    public function setRangeTo($range_to)
    {
        $this->range_to = $range_to;
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