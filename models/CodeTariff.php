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
use Signa\Helpers\Translations as T;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;

class CodeTariff extends Model
{
    protected $id;
    protected $ledger_sales_id;
    protected $signa_tariff_id;
    protected $product_id;
    protected $margin_type;
    protected $margin_value;
    protected $rounding_type;
    protected $margin_type_lab;
    protected $margin_value_lab;
    protected $rounding_type_lab;
    protected $code;
    protected $description;
    protected $price;
    protected $recipe_id;
    protected $added_type;
    protected $active;
    protected $organisation_id;
    protected $options;
    protected $created_by;
    protected $created_at;
    protected $updated_by;
    protected $updated_at;

    private $addedTypeLabels = array(
        1 => 'Manual',
        2 => 'Import'
    );

    public function initialize()
    {
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
        $this->belongsTo('updated_by', 'Signa\Models\Users', 'id', array('alias' => 'UpdatedBy'));
        $this->belongsTo('recipe_id', 'Signa\Models\Recipes', 'id', array('alias' => 'Recipe'));
        $this->belongsTo('product_id', 'Signa\Models\Products', 'id', array('alias' => 'Product'));
        $this->belongsTo('organisation_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Organisation'));
        $this->belongsTo('ledger_sales_id', 'Signa\Models\CodeLedger', 'id', array('alias' => 'LedgerSales'));
        $this->belongsTo('signa_tariff_id', 'Signa\Models\CodeTariff', 'id', array('alias' => 'SignaTariff'));
        $this->hasOne('id', 'Signa\Models\CodeTariff', 'signa_tariff_id', array('alias' => 'LabTariff'));
        $this->hasMany('id', 'Signa\Models\RecipeActivity', 'tariff_id', array('alias' => 'RecipeActivity'));
        $this->hasMany('id', 'Signa\Models\RecipeCustomFieldOptions', 'tariff_id', array('alias' => 'RecipeCustomFieldOptions'));
        $this->setSource("code_tariffs");
    }

    public function validation()
    {
        $validator = new Validation();

        /*$validator->add(
            'code',
            new UniquenessValidator([
                'model' => $this,
                'message' => 'Sorry, that code is already taken.',
            ])
        );*/

        return $this->validate($validator);
    }

    public function setDatas($data)
    {
        if(isset($data['code'])){
            $this->setCode($data['code']);
        }

        if(isset($data['description'])){
            $this->setDescription($data['description']);
        }

        if(isset($data['price'])){
            $this->setPrice($data['price']);
        }

        if(isset($data['recipe_id'])){
            $this->setRecipeId($data['recipe_id']);
        }

        if(isset($data['added_type'])){
            $this->setAddedType($data['added_type']);
        }

        if(isset($data['options'])){
            $this->setOptions(json_encode($data['options']));
        }

        return $this->save();
    }

    public function activateDeactivate($status)
    {
        $this->setActive((int)$status);
        return $this->save();
    }

    public function beforeCreate()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setActive(1);
        $this->setOrganisationId($user->Organisation->getId());
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
    public function getAddedType()
    {
        return $this->addedTypeLabels[$this->added_type];
    }

    /**
     * @param mixed $added_type
     */
    public function setAddedType($added_type)
    {
        $this->added_type = $added_type;
    }

    /**
     * @return mixed
     */
    public function getMarginType(){

        return $this->margin_type;
    }

    /**
     * @param mixed $margin_type
     */
    public function setMarginType($margin_type){

        $this->margin_type = $margin_type;
    }

    /**
     * @return mixed
     */
    public function getRoundingType(){

        return $this->rounding_type;
    }

    /**
     * @param mixed $rounding_type
     */
    public function setRoundingType($rounding_type)
    {
        $this->rounding_type = $rounding_type;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
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
    public function getOptions()
    {
        return json_decode($this->options, true);
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = json_encode($options);
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
    public function getLedgerSalesId()
    {
        return $this->ledger_sales_id;
    }

    /**
     * @param mixed $ledger_sales_id
     */
    public function setLedgerSalesId($ledger_sales_id)
    {
        $this->ledger_sales_id = $ledger_sales_id;
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
    public function getMarginValueLab()
    {
        return $this->margin_value_lab;
    }

    /**
     * @param mixed $margin_value_lab
     */
    public function setMarginValueLab($margin_value_lab)
    {
        $this->margin_value_lab = $margin_value_lab;
    }

    /**
     * @return mixed
     */
    public function getMarginTypeLab(){

        return $this->margin_type_lab;
    }

    /**
     * @param mixed $margin_type_lab
     */
    public function setMarginTypeLab($margin_type_lab){

        $this->margin_type_lab = $margin_type_lab;
    }

    /**
     * @return mixed
     */
    public function getRoundingTypeLab(){

        return $this->rounding_type_lab;
    }

    /**
     * @param mixed $rounding_type_lab
     */
    public function setRoundingTypeLab($rounding_type_lab)
    {
        $this->rounding_type_lab = $rounding_type_lab;
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
}