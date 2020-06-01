<?php
namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\Date;
use Signa\Helpers\Translations;

class OrderCart extends Model
{
    protected $id;
    protected $name;
    protected $organisation_id;
    protected $client_id;
    protected $status;
    protected $supplier_id;
    protected $order_at;
    protected $order_by;
    protected $delivery_at;
    protected $created_by;
    protected $created_at;
    protected $updated_by;
    protected $updated_at;
    protected $deleted_by;
    protected $deleted_at;

    private $statusLabels;
    private $oldStatus;

    public static function getStatusArray()
    {
        $statuses = [
            1 => Translations::make('Open'),
            2 => Translations::make('Confirmed'),
            3 => Translations::make('On delivery'),
            4 => Translations::make('Delivered')
        ];

        return $statuses;
    }

    public function initialize()
    {
        $this->statusLabels = self::getStatusArray();
        $this->belongsTo('organisation_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Organisation'));
        $this->belongsTo('client_id', 'Signa\Models\Users', 'id', array('alias' => 'Client'));
        $this->belongsTo('supplier_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Supplier'));
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
        $this->belongsTo('order_by', 'Signa\Models\Users', 'id', array('alias' => 'OrderBy'));
        $this->hasMany('id', 'Signa\Models\OrderCartProduct', 'order_cart_id', array('alias' => 'OrderCartProduct'));
    }

    public function createNew()
    {
        $user = $this->getDI()->getSession()->get('auth');
        $orderName = $this->generateOrderName();

        $this->setOrganisationId($user->Organisation->getId());
        $this->setStatus(1);
        $this->setName($orderName);
        $this->setClientId($user->getId());
        $this->setCreatedAt(Date::currentDatetime());
        $this->setCreatedBy($user->getId());
        return $this->save();
    }

    public function softDelete()
    {
        $user = $this->getDI()->getSession()->get('auth');
        $this->setDeletedAt(Date::currentDatetime());
        $this->setDeletedBy($user->getId());
        $this->save();
    }

    public function productsSuppliers()
    {
        $suppliersArr = array();
        foreach ($this->OrderCartProduct as $orderCartProduct)
        {
            if(!in_array($orderCartProduct->getSupplierId(), $suppliersArr))
            {
                $suppliersArr[] = $orderCartProduct->getSupplierId();
            }
        }
        return $suppliersArr;
    }

    public function supplierName()
    {
        $orderProducts = $this->OrderCartProduct;
        if(count($orderProducts))
        {
            return $orderProducts[0]->Organisation->getName();
        }
        return 'error';
    }

    private function generateOrderName()
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digits = '0123456789';
        $orderName = '';

        for ($i = 0; $i < 3; $i++) {
            $orderName .= $chars[rand(0, strlen($chars) - 1)];
        }
        for ($i = 0; $i < 7; $i++) {
            $orderName .= $digits[rand(0, strlen($digits) - 1)];
        }

        return $orderName;
    }

    public function afterFetch()
    {
        $this->oldStatus = $this->status;
    }

    public function afterCreate()
    {
//        $this->changeStatusAction();
    }

    public function afterSave()
    {
        $this->changeStatusAction();
    }

    private function changeStatusAction()
    {
        if ($this->oldStatus !== $this->status) {
            // send log to mongo
            $logChangedStatus = new LogLabOrderStatus();
            $logChangedStatus->order_id = $this->id;
            $logChangedStatus->order_name = $this->name;
            $logChangedStatus->order_organisation_id = $this->organisation_id;
            if ($this->Organisation) {
                $logChangedStatus->order_organisation_name = $this->Organisation->getName();
            }
            $logChangedStatus->order_client_id = $this->client_id;
            if ($this->Supplier) {
                $logChangedStatus->order_supplier_name = $this->Supplier->getName();
            }
            $logChangedStatus->order_supplier_id = $this->supplier_id;
            $logChangedStatus->order_status = $this->status;
            $logChangedStatus->order_oldstatus = $this->oldStatus;
            $logChangedStatus->created_at = (new \DateTime())->format('Y-m-d H:i:s');

            $user = $this->getDI()->getSession()->get('auth');
            if ($user) {
                $logChangedStatus->created_by = $user->getId();
            }
            $logChangedStatus->isopened = false ;

            $logChangedStatus->save();
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
    public function getOrderAt()
    {
        return $this->order_at;
    }

    /**
     * @param mixed $order_at
     */
    public function setOrderAt($order_at)
    {
        $this->order_at = $order_at;
    }

    /**
     * @return mixed
     */
    public function getOrderBy()
    {
        return $this->order_by;
    }

    /**
     * @param mixed $order_by
     */
    public function setOrderBy($order_by)
    {
        $this->order_by = $order_by;
    }

    /**
     * @return mixed
     */
    public function getDeliveryAt()
    {
        return $this->delivery_at;
    }

    /**
     * @param mixed $delivery_at
     */
    public function setDeliveryAt($delivery_at)
    {
        $this->delivery_at = $delivery_at;
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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatusLabel()
    {
        return $this->statusLabels[$this->status];
    }
}