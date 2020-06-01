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

class ProductCategories extends Model
{
    protected $id;
    protected $parent_id;
    protected $ledger_purchase_id;
    protected $ledger_sales_id;
    protected $name;
    protected $sort;
    protected $deleted;
    protected $created_at;
    protected $created_by;
    protected $updated_at;
    protected $updated_by;
    protected $deleted_at;
    protected $deleted_by;

    public function initialize()
    {
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'Created'));
        $this->belongsTo('updated_by', 'Signa\Models\Users', 'id', array('alias' => 'Updated'));
        $this->belongsTo('deleted_by', 'Signa\Models\Users', 'id', array('alias' => 'Deleted'));
        $this->belongsTo('parent_id', 'Signa\Models\ProductCategories', 'id', array('alias' => 'Parent'));
        $this->belongsTo('ledger_purchase_id', 'Signa\Models\CodeLedger', 'id', array('alias' => 'LedgerPurchase'));
        $this->belongsTo('ledger_sales_id', 'Signa\Models\CodeLedger', 'id', array('alias' => 'LedgerSales'));

        $this->hasMany('id', 'Signa\Models\ProductCategories', 'parent_id', array('alias' => 'Children'));
        $this->hasMany('id', 'Signa\Models\Products', 'category_id', array('alias' => 'Products'));
    }

    public function saveData($data)
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setCreatedAt(Date::currentDatetime());
        $this->setCreatedBy($user->getId());
        $this->setName($data['name']);
        $this->setParentId($data['parent_id']);
        $this->setDeleted(0);

        return $this->save();
    }

    public function softDelete()
    {
        $user = $this->getDI()->getSession()->get('auth');
        $this->setDeletedAt(Date::currentDatetime());
        $this->setDeletedBy($user->getId());
        $this->setDeleted(1);
        $this->save();
    }

    public function beforeUpdate()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setUpdatedAt(Date::currentDatetime());
        $this->setUpdatedBy($user->getId());
    }

    public function beforeSave()
    {
        // fix for acc server
        if ($this->parent_id == '') {
            $this->parent_id = null;
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
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * @param mixed $parent_id
     */
    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @return mixed
     */
    public function getLedgerPurchaseId()
    {
        return $this->ledger_purchase_id;
    }

    /**
     * @return mixed
     */
    public function getLedgerSalesId()
    {
        return $this->ledger_sales_id;
    }

    /**
     * @param mixed $ledger_purchase_id
     */
    public function setLedgerPurchaseId($ledger_purchase_id)
    {
        $this->ledger_purchase_id = $ledger_purchase_id;
    }

    /**
     * @param mixed $ledger_sales_id
     */
    public function setLedgerSalesId($ledger_sales_id)
    {
        $this->ledger_sales_id = $ledger_sales_id;
    }

    /**
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param mixed $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
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