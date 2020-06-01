<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 15.07.2016
 * Time: 12:13
 */

namespace Signa\Models;

use Phalcon\Mvc\Model;

class CategoryTreeRecipes extends Model
{
    protected $category_tree_id;
    protected $recipe_id;

    public function initialize()
    {
        $this->belongsTo('category_tree_id', 'Signa\Models\CategoryTree', 'id', array('alias' => 'CategoryTree'));
        $this->belongsTo('recipe_id', 'Signa\Models\Recipes', 'id', array('alias' => 'Recipes'));
    }

    public function saveData($data)
    {
        $this->setRecipeId($data['product']);
        $this->setCategoryTreeId($data['id']);

        return $this->save();
    }


    /**
     * @return mixed
     */
    public function getCategoryTreeId()
    {
        return $this->category_tree_id;
    }

    /**
     * @param mixed $category_tree_id
     */
    public function setCategoryTreeId($category_tree_id)
    {
        $this->category_tree_id = $category_tree_id;
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


}