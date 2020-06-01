<?php
namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\Date;

class InvoicesBulk extends Model
{
    protected $id;
    protected $lab_id;
    protected $bulk_status;
    protected $date;
    protected $start_period;
    protected $end_period;
    protected $created_at;
    protected $created_by;
    protected $updated_at;
    protected $updated_by;
    protected $deleted_at;
    protected $deleted_by;

    public function initialize()
    {
        $this->belongsTo('deleted_by', 'Signa\Models\Users', 'id', array('alias' => 'DeletedBy'));
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
        $this->belongsTo('updated_by', 'Signa\Models\Users', 'id', array('alias' => 'UpdatedBy'));
        $this->belongsTo('lab_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Lab'));
        $this->hasMany('id', 'Signa\Models\Invoices', 'bulk_id', array('alias' => 'Invoices'));
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

    public function softDelete()
    {
        $user = $this->getDI()->getSession()->get('auth');
        $this->setDeletedBy($user->getId());
        $this->setDeletedAt(Date::currentDatetime());
        $this->save();
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
    public function getLabId()
    {
        return $this->lab_id;
    }

    /**
     * @param mixed $lab_id
     */
    public function setLabId($lab_id)
    {
        $this->lab_id = $lab_id;
    }

    /**
     * @return mixed
     */
    public function getBulkStatus()
    {
        return $this->bulk_status;
    }

    /**
     * @param mixed $bulk_status
     */
    public function setBulkStatus($bulk_status)
    {
        $this->bulk_status = $bulk_status;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getStartPeriod()
    {
        return $this->start_period;
    }

    /**
     * @param mixed $start_period
     */
    public function setStartPeriod($start_period)
    {
        $this->start_period = $start_period;
    }

    /**
     * @return mixed
     */
    public function getEndPeriod()
    {
        return $this->end_period;
    }

    /**
     * @param mixed $end_period
     */
    public function setEndPeriod($end_period)
    {
        $this->end_period = $end_period;
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