<?php
namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\Date;

class RecipeCustomFieldOptions extends Model
{
    protected $id;
    protected $recipe_customfield_id;
    protected $option;
    protected $value;
    protected $tariff_id;
    protected $custom_price_tariff_id;
    protected $amount;
    protected $created_by;
    protected $created_at;
    protected $updated_by;
    protected $updated_at;

    public function initialize()
    {
        $this->belongsTo('recipe_customfield_id', 'Signa\Models\RecipeCustomField', 'id', array('alias' => 'RecipeCustomField'));
        $this->belongsTo('tariff_id', 'Signa\Models\CodeTariff', 'id', array('alias' => 'Tariff'));
        $this->belongsTo('custom_price_tariff_id', 'Signa\Models\CodeTariff', 'id', array('alias' => 'CustomTariff'));
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
    public function getRecipeCustomfieldId()
    {
        return $this->recipe_customfield_id;
    }

    /**
     * @param mixed $recipe_customfield_id
     */
    public function setRecipeCustomfieldId($recipe_customfield_id)
    {
        $this->recipe_customfield_id = $recipe_customfield_id;
    }

    /**
     * @return mixed
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param mixed $option
     */
    public function setOption($option)
    {
        $this->option = $option;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
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
    public function getCustomPriceTariffId()
    {
        return $this->custom_price_tariff_id;
    }

    /**
     * @param mixed $custom_price_tariff_id
     */
    public function setCustomPriceTariffId($custom_price_tariff_id)
    {
        if ($custom_price_tariff_id == '') { // fix for DB
            $custom_price_tariff_id = null;
        }
        $this->custom_price_tariff_id = $custom_price_tariff_id;
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
}