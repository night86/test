<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 15.07.2016
 * Time: 12:13
 */

namespace Signa\Models;

use Phalcon\Mvc\Model;

class CountlistProducts extends Model
{
    protected $id;
    protected $countlist_id;
    protected $product_id;
    protected $product_name;
    protected $product_amount;
    protected $product_price;
    protected $created_by;
    protected $created_at;

    public function initialize()
    {
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
        $this->belongsTo('product_id', 'Signa\Models\Products', 'id', array('alias' => 'Product'));
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
    public function getCountlistId()
    {
        return $this->countlist_id;
    }

    /**
     * @param mixed $countlist_id
     */
    public function setCountlistId($countlist_id)
    {
        $this->countlist_id = $countlist_id;
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
    public function getProductName()
    {
        return $this->product_name;
    }

    /**
     * @param mixed $product_name
     */
    public function setProductName($product_name)
    {
        $this->product_name = $product_name;
    }

    /**
     * @return mixed
     */
    public function getProductAmount()
    {
        return $this->product_amount;
    }

    /**
     * @param mixed $product_amount
     */
    public function setProductAmount($product_amount)
    {
        $this->product_amount = $product_amount;
    }

    /**
     * @return mixed
     */
    public function getProductPrice()
    {
        return $this->product_price;
    }

    /**
     * @param mixed $product_price
     */
    public function setProductPrice($product_price)
    {
        $this->product_price = $product_price;
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
}