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

class ImportProducts extends Model
{
    protected $id;
    protected $type;
    protected $filename;
    protected $effective_from;
    protected $supplier_id;
    protected $closed;
    protected $message;
    protected $created_at;
    protected $created_by;
    protected $updated_at;
    protected $updated_by;
    protected $isopened;
    protected $approve_in_progress;

    public function initialize()
    {
        $this->belongsTo('supplier_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Organisation'));
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'Created'));
        $this->belongsTo('updated_by', 'Signa\Models\Users', 'id', array('alias' => 'Updated'));

        $this->hasMany('id', 'Signa\Models\Products', 'import_id', array('alias' => 'Products'));
    }

    public function saveData($dataArr)
    {

        $user = $this->getDI()->getSession()->get('auth');

        if(isset($dataArr['type']) && !is_null($dataArr['type']))
            $this->setType($dataArr['type']);
        if(isset($dataArr['effective_from']) && !is_null($dataArr['effective_from']))
            $this->setEffectiveFrom($dataArr['effective_from']);
        if(isset($dataArr['closed']) && !is_null($dataArr['closed']))
            $this->setClosed($dataArr['closed']);
        if(isset($dataArr['filename']) && !is_null($dataArr['filename']))
            $this->setFilename($dataArr['filename']);

        $this->setSupplierId($user->Organisation->getId());
        $this->setCreatedAt(Date::currentDatetime());
        $this->setCreatedBy($user->getId());

        return $this->save();
    }

    public function productsToImport()
    {
        return Products::find("deleted = 0 AND approved = 0 AND declined = 0 AND active = 0 AND waiting_for_approve IS NULL AND import_id = ".$this->getId());
    }

    public function selectedProductsToImport(array $productsIdList, $type = '')
    {
        if(count($productsIdList))
        {
            $ids = implode(",", $productsIdList);
            return Products::find("deleted = 0 AND approved = 0 AND declined = 0 AND id ".$type." IN (".$ids.") AND import_id = ".$this->getId());
        }
        return $this->productsToImport();
    }

    public function checkProducts()
    {
        $products = $this->productsToImport();
        if(count($products) == 0)
        {
            $this->setClosed(1);
            return $this->save();
        }
        return false;
    }

    public function getDateTimeArr()
    {
        return Date::dateTimeToArr($this->getCreatedAt());
    }

    public function beforeUpdate()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setUpdatedAt(Date::currentDatetime());
        $this->setUpdatedBy($user->getId());
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return mixed
     */
    public function getEffectiveFrom()
    {
        return $this->effective_from;
    }

    /**
     * @param mixed $effective_from
     */
    public function setEffectiveFrom($effective_from)
    {
        $this->effective_from = $effective_from;
    }

    /**
     * @return mixed
     */
    public function getSupplierId()
    {
        return $this->supplier_id;
    }

    /**
     * @param mixed $supplier_id
     */
    public function setSupplierId($supplier_id)
    {
        $this->supplier_id = $supplier_id;
    }

    /**
     * @return mixed
     */
    public function getClosed()
    {
        return $this->closed;
    }

    /**
     * @param mixed $closed
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
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
    public function getIsopened()
    {
        return $this->isopened;
    }

    /**
     * @param mixed $isopened
     */
    public function setIsopened($isopened)
    {
        $this->isopened = $isopened;
    }

    /**
     * @return mixed
     */
    public function getApproveInProgress()
    {
        return $this->approve_in_progress;
    }

    /**
     * @param mixed $approve_in_progress
     */
    public function setApproveInProgress($approve_in_progress)
    {
        $this->approve_in_progress = $approve_in_progress;
    }
}