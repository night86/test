<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 15.07.2016
 * Time: 12:13
 */

namespace Signa\Models;

use Phalcon\Builder\Options;
use Phalcon\Mvc\Model;
use Signa\Helpers\Date;
use Signa\Libs\Recipes as RecipeLib;

class Recipes extends Model
{
    protected $id;
    protected $organisation_id;
    protected $lab_id;
    protected $product_id;
    protected $parent_id;
    protected $code;
    protected $recipe_number;
    protected $name;
    protected $description;
    protected $image;
    protected $price_type;
    protected $price;
    protected $custom_code;
    protected $custom_name;
    protected $custom_recipe;
    protected $delivery_time;
    protected $active;
    protected $statuses;
    protected $has_schema;
    protected $schema_notice;
    protected $is_basic;
    protected $created_by;
    protected $created_at;
    protected $updated_by;
    protected $updated_at;
    protected $deleted_by;
    protected $deleted_at;

    private $priceTypeLabels = array(
        1 => 'Composite',
        2 => 'Fixed'
    );

    const customFieldTypes = array(
        'text'      => 'Text',
        'number'    => 'Number',
        'checkbox'  => 'Checkboxes',
        'select'    => 'Select list',
        'statement' => 'Statement',
        'textarea'  => 'Text area'
    );

    private $customFieldTypes = self::customFieldTypes;

    public function initialize()
    {
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
        $this->belongsTo('updated_by', 'Signa\Models\Users', 'id', array('alias' => 'UpdatedBy'));
        $this->belongsTo('product_id', 'Signa\Models\Products', 'id', array('alias' => 'Product'));
        $this->belongsTo('parent_id', 'Signa\Models\Recipes', 'id', array('alias' => 'ParentRecipe'));
        $this->belongsTo('organisation_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Organisation'));
        $this->belongsTo('lab_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Lab'));
        $this->hasMany('id', 'Signa\Models\Recipes', 'parent_id', array('alias' => 'RecipeChildren'));
        $this->hasMany('id', 'Signa\Models\RecipeActivity', 'recipe_id', array('alias' => 'RecipeActivity'));
        $this->hasMany('id', 'Signa\Models\RecipeProduct', 'recipe_id', array('alias' => 'RecipeProduct'));
        $this->hasMany('id', 'Signa\Models\RecipeSetting', 'recipe_id', array('alias' => 'RecipeSettings'));
        $this->hasMany('id', 'Signa\Models\RecipeCustomField', 'recipe_id', array(
            'alias' => 'RecipeCustomField',
            'params' => [
                'order' => 'id ASC'
            ]
        ));
        $this->hasMany('code', 'Signa\Models\DentistGroupDiscount', 'code', array('alias' => 'DGD'));
        $this->hasManyToMany(
            'id',
            'Signa\Models\CategoryTreeRecipes',
            'recipe_id',
            'category_tree_id',
            'Signa\Models\CategoryTree',
            'id',
            array('alias' => 'CategoryTree')
        );
        $this->hasManyToMany(
            'id',
            'Signa\Models\DentistOrderRecipe',
            'recipe_id',
            'order_id',
            'Signa\Models\DentistOrder',
            'id',
            array('alias' => 'DentistOrder')
        );
    }

    public function getActiveChildren()
    {
        $children = array();
        foreach ($this->RecipeChildren as $recipe)
        {
            if (!$recipe->getDeletedAt() && $recipe->getActive() == 1 && $recipe->getLabId()) {
                $children[] = $recipe;
            }
        }

        return $children;
    }

    public function activateDeactivate($status)
    {
        $this->setActive((int)$status);
        return $this->save();
    }

    public function checkIfCanBeActivated()
    {
        if ($this->price_type == 2) { // fixed price
            return true;
        }

        $tariffs = CodeTariff::find(
            array(
                'active = :active: AND organisation_id = :organisation_id:',
                'bind' => array(
                    'active' => 1,
                    'organisation_id' => $this->organisation_id
                )
            )
        );

        $mappedSignaTariffs = [];
        foreach ($tariffs as $tariff) {
            $map = MapLabTariffLedger::findFirstByTariffId($tariff->getId());
            if ($map) {
                $mappedSignaTariffs[] = $map->getSignaTariffId();
            }
        }

        $neededSignaTariffs = [];
        $customFields = RecipeCustomField::find(
            array(
                'recipe_id = :recipe:',
                'bind' =>
                    [
                        'recipe' => $this->getParentId(),
                    ]
            )
        );

        foreach ($customFields as $customField) {
            foreach ($customField->Options as $option) {
                if ($option->Tariff) {
                    $neededSignaTariffs[] = $option->Tariff->getId();
                }
            }
        }

        foreach ($this->ParentRecipe->RecipeActivity as $recipeActivity) {
            if ($recipeActivity->Tariff) {
                $neededSignaTariffs[] = $recipeActivity->Tariff->getId();
            }
        }

        $result = array_diff($neededSignaTariffs, $mappedSignaTariffs);

        if (empty($result)) {
            return true;
        } else {
            return true;
        }
//        print_r($mappedSignaTariffs);
//        print_r($neededSignaTariffs);
//        print_r($result);
//        die;
    }

    public function beforeCreate()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setActive(0);
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

    public function softDelete()
    {
        $user = $this->getDI()->getSession()->get('auth');
        $this->setDeletedBy($user->getId());
        $this->setDeletedAt(Date::currentDatetime());
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
    public function getRecipeNumber()
    {
        return $this->recipe_number;
    }

    /**
     * @param mixed $recipe_number
     */
    public function setRecipeNumber($recipe_number)
    {
        $this->recipe_number = $recipe_number;
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
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
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
    public function getCustomCode()
    {
        return $this->custom_code;
    }

    /**
     * @param mixed $custom_code
     */
    public function setCustomCode($custom_code)
    {
        $this->custom_code = $custom_code;
    }

    /**
     * @return mixed
     */
    public function getCustomName()
    {
        return $this->custom_name;
    }

    /**
     * @param mixed $custom_name
     */
    public function setCustomName($custom_name)
    {
        $this->custom_name = $custom_name;
    }

    /**
     * @return mixed
     */
    public function getCustomRecipe()
    {
        return $this->custom_recipe;
    }

    /**
     * @param mixed $custom_recipe
     */
    public function setCustomRecipe($custom_recipe)
    {
        $this->custom_recipe = $custom_recipe;
    }

    /**
     * @return mixed
     */
    public function getDeliveryTime()
    {
        return $this->delivery_time;
    }

    /**
     * @param mixed $delivery_time
     */
    public function setDeliveryTime($delivery_time)
    {
        $this->delivery_time = $delivery_time;
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
    public function getAddedType()
    {
        return $this->priceTypeLabels[$this->price_type];
    }

    /**
     * @param mixed $price_type
     */
    public function setPriceType($price_type)
    {
        $this->price_type = $price_type;
    }

    /**
     * @return array
     */
    public function getPriceType()
    {
        return $this->priceTypeLabels[$this->price_type];
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
     * @return array
     */
    public function getCustomFieldTypes()
    {
        return $this->customFieldTypes;
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     *
     */
    public function setCode()
    {
        $this->code = RecipeLib::generateCode();
    }

    /**
     * @return array
     */
    public function getCategriesSringArray()
    {
        if ($this->ParentRecipe) {
            return RecipeLib::getCategories($this->ParentRecipe);
        } else {
            return RecipeLib::getCategories($this);
        }
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * @param mixed $parent_id
     */
    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @return mixed
     */
    public function getLabId()
    {
        return $this->lab_id;
    }

    /**
     * @param mixed $lab_id
     */
    public function setLabId($lab_id)
    {
        $this->lab_id = $lab_id;
    }

    /**
     * @return mixed
     */
    public function getStatuses()
    {
        return $this->statuses;
    }

    /**
     * @param mixed $statuses
     */
    public function setStatuses($statuses)
    {
        $this->statuses = $statuses;
    }

    /**
     * @return mixed
     */
    public function getHasSchema()
    {
        return $this->has_schema;
    }

    /**
     * @param mixed $has_schema
     */
    public function setHasSchema($has_schema)
    {
        $this->has_schema = $has_schema;
    }

    /**
     * @return mixed
     */
    public function getSchemaNotice()
    {
        return $this->schema_notice;
    }

    /**
     * @param mixed $schema_notice
     */
    public function setSchemaNotice($schema_notice)
    {
        $this->schema_notice = $schema_notice;
    }

    /**
     * @return mixed
     */
    public function getIsBasic()
    {
        return $this->is_basic;
    }

    /**
     * @param mixed $is_basic
     */
    public function setIsBasic($is_basic)
    {
        $this->is_basic = $is_basic;
    }

}