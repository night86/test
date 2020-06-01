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

class RecipeDefaultSettingOption extends Model
{
    protected $id;
    protected $recipe_default_setting_id;
    protected $name;
    protected $howManyRecipes = 0;

    public function initialize()
    {
        $this->belongsTo('recipe_default_setting_id', 'Signa\Models\RecipeDefaultSetting', 'id', array('alias' => 'DefaultSetting'));
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
    public function getRecipeDefaultSettingId()
    {
        return $this->recipe_default_setting_id;
    }

    /**
     * @param mixed $recipe_default_setting_id
     */
    public function setRecipeDefaultSettingId($recipe_default_setting_id)
    {
        $this->recipe_default_setting_id = $recipe_default_setting_id;
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
}