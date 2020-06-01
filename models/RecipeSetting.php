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
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;

class RecipeSetting extends Model
{
    protected $id;
    protected $recipe_id;
    protected $setting_id;
    protected $option_id;
    protected $created_by;
    protected $created_at;
    protected $updated_by;
    protected $updated_at;

    public function initialize()
    {
        $this->belongsTo('recipe_id', 'Signa\Models\Recipes', 'id', array('alias' => 'Recipe'));
        $this->belongsTo('setting_id', 'Signa\Models\RecipeSetting', 'id', array('alias' => 'Setting'));
        $this->belongsTo('option_id', 'Signa\Models\RecipeDefaultSettingOption', 'id', array('alias' => 'Option'));
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
    public function getSettingId()
    {
        return $this->setting_id;
    }

    /**
     * @param mixed $setting_id
     */
    public function setSettingId($setting_id)
    {
        $this->setting_id = $setting_id;
    }

    /**
     * @return mixed
     */
    public function getOptionId()
    {
        return $this->option_id;
    }

    /**
     * @param mixed $option_id
     */
    public function setOptionId($option_id)
    {
        $this->option_id = $option_id;
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