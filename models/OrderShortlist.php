<?php
namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\Date;

class OrderShortlist extends Model
{
    protected $id;
    protected $organisation_id;
    protected $product_id;
    protected $tariff_id;
    protected $ledger_id;
    protected $amount_min;
    protected $margin_type;
    protected $margin_value;
    protected $round_direction;
    protected $round_type;
    protected $created_by;
    protected $created_at;
    protected $deleted_by;
    protected $deleted_at;

    private $marginTypeLabels = array(
        1 => 'Fixed price',
        2 => 'Fixed margin in euro',
        3 => 'As percentages of the purchase price',
        4 => 'As percentages of the sales price'
    );

    private $roundDirectionLabels = array(
        1 => 'Up',
        2 => 'Down'
    );

    private $roundTypeLabels = array(
        1 => 'Decimal',
        2 => 'Integer'
    );

    public function initialize()
    {
        $this->belongsTo('organisation_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Organisation'));
        $this->belongsTo('tariff_id', 'Signa\Models\CodeTariff', 'id', array('alias' => 'Tariff'));
        $this->belongsTo('ledger_id', 'Signa\Models\CodeLedger', 'id', array('alias' => 'Ledger'));
        $this->belongsTo('product_id', 'Signa\Models\Products', 'id', array('alias' => 'Product'));
    }

    public function createNew($productId, $amountMin, $tariff_id = null, $ledger_id = null)
    {
        $amountMin = is_null($amountMin) ? 1 : $amountMin;
        $user = $this->getDI()->getSession()->get('auth');

        $this->setOrganisationId($user->Organisation->getId());
        $this->setProductId($productId);
        if(!is_null($tariff_id))
            $this->setTariffId($tariff_id);
        if(!is_null($ledger_id))
            $this->setLedgerId($ledger_id);
        $this->setAmountMin($amountMin);
        $this->setCreatedAt(Date::currentDatetime());
        $this->setCreatedBy($user->getId());
        return $this->save();
    }

    public function getProductPrice($discounted = true)
    {
        $productPrice=0;
        $product = Products::getCurrentProduct($this->getProductId());
        if($product) {
            $productPrice = $product->getPrice($discounted);
        }
        $newPrice = $productPrice;
        $marginType = $this->getMarginTypeValue();
        $roundDirection = $this->getRoundDirection();
        $roundType = $this->getRoundType();
        if(!is_null($marginType))
        {
            switch((int)$marginType)
            {
                case 1:
                    $newPrice = $this->getMarginValue();
                    break;
                case 2:
                    $newPrice = $this->getMarginValue() + $productPrice;
                    break;
                case 3:
                    $newPrice = $productPrice + ($this->getMarginValue() / 100) * $productPrice;
                    break;
                case 4:
                    $newPrice = $productPrice / (100 - $this->getMarginValue()) * 100;
                    break;
            }
            if($roundDirection == 1)
            {
                if($roundType == 1)
                {
                    $newPrice = round($newPrice, 2);
                }else{
                    $newPrice = ceil($newPrice);
                }
            }else
            {
                if($roundType == 1)
                {
                    $newPrice = floor($newPrice * 100) / 100;
                }else{
                    $newPrice = floor($newPrice);
                }
            }
        }
        return number_format($newPrice, 2, '.', ' ');
    }

    public function softDelete()
    {
        $user = $this->getDI()->getSession()->get('auth');
        $this->setDeletedAt(Date::currentDatetime());
        $this->setDeletedBy($user->getId());
        $this->save();
    }

    public function beforeDelete()
    {
        $this->forceToUpdateProduct();
    }

    public function beforeCreate()
    {
        $this->forceToUpdateProduct($this->getProductId());
    }

    public function beforeUpdate()
    {
        $this->forceToUpdateProduct();
    }

    private function forceToUpdateProduct($productId = null)
    {
        if ($productId) {
            $this->getDi()->getShared('db')->execute(
                'UPDATE products SET need_update = 1 WHERE id = '.$productId
            );
        } else {
            $product = Products::getCurrentProduct($this->getProductId());
            if ($product) {
                $product->setNeedUpdate(1);
                $product->save();
            }
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
    public function getAmountMin()
    {
        return $this->amount_min;
    }

    /**
     * @param mixed $amount_min
     */
    public function setAmountMin($amount_min)
    {
        $this->amount_min = $amount_min;
    }

    /**
     * @return mixed
     */
    public function getMarginType()
    {
        return $this->marginTypeLabels[$this->margin_type];
    }

    /**
     * @param mixed $margin_type
     */
    public function setMarginType($margin_type)
    {
        $this->margin_type = $margin_type;
    }

    /**
     * @return mixed
     */
    public function getMarginTypeValue()
    {
        return $this->margin_type;
    }

    /**
     * @return mixed
     */
    public function getMarginValue()
    {
        return $this->margin_value;
    }

    /**
     * @param mixed $margin_value
     */
    public function setMarginValue($margin_value)
    {
        $this->margin_value = $margin_value;
    }

    /**
     * @return mixed
     */
    public function getRoundDirection()
    {
        return $this->round_direction;
    }

    /**
     * @param mixed $round_derection
     */
    public function setRoundDirection($round_direction)
    {
        $this->round_direction = $round_direction;
    }

    /**
     * @return mixed
     */
    public function getRoundType()
    {
        return $this->round_type;
    }

    /**
     * @param mixed $round_type
     */
    public function setRoundType($round_type)
    {
        $this->round_type = $round_type;
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