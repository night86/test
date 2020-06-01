<?php
namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\Date;
use Signa\Helpers\Models;

class RecipeCustomField extends Model
{
    protected $id;
    protected $recipe_id;
    protected $name;
    protected $type;
    protected $custom_price_tariff_id;
    protected $custom_price_type;
    protected $custom_field_type;
    protected $has_lab_check;
    protected $amount;
    protected $params;
    protected $created_by;
    protected $created_at;
    protected $updated_by;
    protected $updated_at;


    public function initialize()
    {
        $this->belongsTo('recipe_id', 'Signa\Models\Recipes', 'id', array('alias' => 'Recipe'));
        $this->belongsTo('custom_price_tariff_id', 'Signa\Models\CodeTariff', 'id', array('alias' => 'Tariff'));
        $this->hasMany('id', 'Signa\Models\RecipeCustomFieldOptions', 'recipe_customfield_id', array('alias' => 'Options'));
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

    public function beforeSave() {
        $this->serializeData();
    }

    public function afterSave() {
        $this->unserializeData();
    }

    public function afterFetch() {
        $this->unserializeData();
    }

    private function unserializeData() {
        $this->params = Models::reunserialize($this->params ? unserialize($this->params) : array());
    }

    private function serializeData() {
        $this->params = Models::serializeBeforeSave($this->params);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getRecipeId()
    {
        return $this->recipe_id;
    }

    /**
     * @param mixed $recipe_id
     */
    public function setRecipeId($recipe_id)
    {
        $this->recipe_id = $recipe_id;
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
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $params
     */
    public function setParams($params)
    {
        $this->params = $params;
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
    public function getCustomPriceType()
    {
        return $this->custom_price_type;
    }

    /**
     * @param mixed $custom_price_type
     */
    public function setCustomPriceType($custom_price_type)
    {
        $this->custom_price_type = $custom_price_type;
    }

    /**
     * @return mixed
     */
    public function getCustomFieldType()
    {
        return $this->custom_field_type;
    }

    /**
     * @param mixed $custom_field_type
     */
    public function setCustomFieldType($custom_field_type)
    {
        $this->custom_field_type = $custom_field_type;
    }

    /**
     * @return mixed
     */
    public function getHasLabCheck()
    {
        return $this->has_lab_check;
    }

    /**
     * @param mixed $has_lab_check
     */
    public function setHasLabCheck($has_lab_check)
    {
        $this->has_lab_check = $has_lab_check;
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