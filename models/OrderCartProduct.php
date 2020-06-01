<?php
namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\Date;

class OrderCartProduct extends Model
{
    protected $id;
    protected $order_cart_id;
    protected $product_id;
    protected $price;
    protected $project_no;
    protected $supplier_id;
    protected $amount;
    protected $received;
    protected $created_by;
    protected $created_at;
    protected $sent_at;

    public function initialize()
    {
        $this->belongsTo('order_cart_id', 'Signa\Models\OrderCart', 'id', array('alias' => 'OrderCart'));
        $this->belongsTo('product_id', 'Signa\Models\Products', 'id', array('alias' => 'Product'));
        $this->belongsTo('supplier_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Organisation'));
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
    }

    public function saveData(array $data)
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setAmount($data['amount']);
        $this->setProductId($data['product_id']);
        $this->setSupplierId($data['supplier_id']);
        $this->setOrderCartId($data['order_cart_id']);
        if(isset($data['project_no']))
        {
            $this->setProjectNo($data['project_no']);
        }
        $this->setCreatedAt(Date::currentDatetime());
        $this->setCreatedBy($user->getId());

        return $this->save();
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
    public function getOrderCartId()
    {
        return $this->order_cart_id;
    }

    /**
     * @param mixed $order_cart_id
     */
    public function setOrderCartId($order_cart_id)
    {
        $this->order_cart_id = $order_cart_id;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * @param mixed $product_id
     */
    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getProjectNo()
    {
        return $this->project_no;
    }

    /**
     * @param mixed $project_no
     */
    public function setProjectNo($project_no)
    {
        $this->project_no = $project_no;
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
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
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
    public function getReceived()
    {
        return $this->received;
    }

    /**
     * @param mixed $received
     */
    public function setReceived($received)
    {
        $this->received = $received;
    }

    /**
     * @return mixed
     */
    public function getSentAt()
    {
        return $this->sent_at;
    }

    /**
     * @param mixed $sent_at
     */
    public function setSentAt($sent_at)
    {
        $this->sent_at = $sent_at;
    }

    public function setSetAtAsCurrentDate(){
        $this->setSentAt(Date::currentDatetime());
    }



}