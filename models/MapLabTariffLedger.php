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

class MapLabTariffLedger extends Model
{
    protected $id;
    protected $tariff_id;
    protected $signa_tariff_id;
    protected $ledger_id;
    protected $product_id;
    protected $created_by;
    protected $created_at;
    protected $updated_by;
    protected $updated_at;

    public function initialize()
    {
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
        $this->belongsTo('updated_by', 'Signa\Models\Users', 'id', array('alias' => 'UpdatedBy'));
        $this->belongsTo('product_id', 'Signa\Models\Products', 'id', array('alias' => 'Product'));
        $this->belongsTo('ledger_id', 'Signa\Models\CodeLedger', 'id', array('alias' => 'Ledger'));
        $this->belongsTo('tariff_id', 'Signa\Models\CodeTariff', 'id', array('alias' => 'Tariff'));
        $this->belongsTo('signa_tariff_id', 'Signa\Models\CodeTariff', 'id', array('alias' => 'SignaTariff'));
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
    public function getTariffId()
    {
        return $this->tariff_id;
    }

    /**
     * @param mixed $tariff_id
     */
    public function setTariffId($tariff_id)
    {
        $this->tariff_id = $tariff_id;
    }

    /**
     * @return mixed
     */
    public function getLedgerId()
    {
        return $this->ledger_id;
    }

    /**
     * @param mixed $ledger_id
     */
    public function setLedgerId($ledger_id)
    {
        $this->ledger_id = $ledger_id;
    }

    /**
     * @return mixed
     */
    public function getSignaTariffId()
    {
        return $this->signa_tariff_id;
    }

    /**
     * @param mixed $signa_tariff_id
     */
    public function setSignaTariffId($signa_tariff_id)
    {
        $this->signa_tariff_id = $signa_tariff_id;
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

}