<?php
namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\Date;

class DentistOrderRecipeDataOptions extends Model
{
    protected $id;
    protected $dentist_order_recipe_data_id;
    protected $option;
    protected $value;
    protected $tariff_id;
    protected $custom_price_tariff_id;
    protected $created_by;
    protected $created_at;
    protected $updated_by;
    protected $updated_at;
    protected $tariff_options;

    public function initialize()
    {
        $this->belongsTo('dentist_order_recipe_data_id', 'Signa\Models\DentistOrderRecipeData', 'id', array('alias' => 'DentistOrderRecipeData'));
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
    public function getDentistOrderRecipeDataId()
    {
        return $this->dentist_order_recipe_data_id;
    }

    /**
     * @param mixed $dentist_order_recipe_data_id
     */
    public function setDentistOrderRecipeDataId($dentist_order_recipe_data_id)
    {
        $this->dentist_order_recipe_data_id = $dentist_order_recipe_data_id;
    }

    /**
     * @return mixed
     */
    public function getTariffOptions()
    {
        return json_decode($this->tariff_options, true);
    }

    /**
     * @param mixed $tariff_options
     */
    public function setTariffOptions($tariff_options)
    {
        $this->tariff_options = json_encode($tariff_options);
    }
}