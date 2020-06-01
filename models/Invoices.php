<?php
namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\General;
use Signa\Helpers\Date;

class Invoices extends Model
{
    protected $id;
    protected $client_id;
    protected $bulk_id;
    protected $seller_id;
    protected $number;
    protected $description;
    protected $date;
    protected $due_date;
    protected $invoice_status;
    protected $invoice_type;
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
        $this->belongsTo('client_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Client'));
        $this->belongsTo('seller_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Seller'));
        $this->belongsTo('bulk_id', 'Signa\Models\InvoicesBulk', 'id', array('alias' => 'InvoicesBulk'));
        $this->hasMany('id', 'Signa\Models\InvoiceRecords', 'invoice_id', array('alias' => 'Records'));
        $this->hasOne('id', 'Signa\Models\DeliveryNotes', 'invoice_id', array('alias' => 'DeliveryNote'));
    }

    public function beforeSave()
    {
        $this->date = date_format(new \Datetime($this->date), 'Y-m-d');
        $this->due_date = date_format(new \Datetime($this->due_date), 'Y-m-d');
    }

    public function saveData($data)
    {
        if(isset($data['description'])){
            $this->setDescription($data['description']);
        }

        if(isset($data['date'])){
            $this->setDate($data['date']);
        }

        if(isset($data['due_date'])){
            $this->setDueDate($data['due_date']);
        }

        if(isset($data['client_data'])){
            $this->setClientData(json_encode($data['client_data']));
        }

        if(isset($data['invoice_status'])){
            $this->setInvoiceStatus($data['invoice_status']);
        }

        if(isset($data['invoice_type'])){
            $this->setInvoiceType($data['invoice_type']);
        }

        return $this->save();
    }

    public function getAmount()
    {
        $amount = 0;
        $records = $this->Records;
        foreach($records as $record)
        {
            $totalRecord = $record->getPrice() * $record->getAmount();
            $amount += round($totalRecord + $totalRecord * ($record->getTax() / 100), 2);
        }

        return $amount;
    }

    public function beforeCreate()
    {
        $user = $this->getDI()->getSession()->get('auth');

//        $this->setNumber(General::generateInvoiceNumber());
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
        $this->setDeletedAt(Date::currentDatetime());
        $this->setDeletedBy($user->getId());
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
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * @param mixed $client_id
     */
    public function setClientId($client_id)
    {
        $this->client_id = $client_id;
    }

    /**
     * @return mixed
     */
    public function getBulkId()
    {
        return $this->bulk_id;
    }

    /**
     * @param mixed $bulk_id
     */
    public function setBulkId($bulk_id)
    {
        $this->bulk_id = $bulk_id;
    }

    /**
     * @return mixed
     */
    public function getSellerId()
    {
        return $this->seller_id;
    }

    /**
     * @param mixed $seller_id
     */
    public function setSellerId($seller_id)
    {
        $this->seller_id = $seller_id;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
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
    public function getDueDate()
    {
        return $this->due_date;
    }

    /**
     * @param mixed $due_date
     */
    public function setDueDate($due_date)
    {
        $this->due_date = $due_date;
    }

    /**
     * @return mixed
     */
    public function getInvoiceStatus()
    {
        return $this->invoice_status;
    }

    /**
     * @param mixed $invoice_status
     */
    public function setInvoiceStatus($invoice_status)
    {
        $this->invoice_status = $invoice_status;
    }

    /**
     * @return mixed
     */
    public function getInvoiceType()
    {
        return $this->invoice_type;
    }

    /**
     * @param mixed $invoice_type
     */
    public function setInvoiceType($invoice_type)
    {
        $this->invoice_type = $invoice_type;
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