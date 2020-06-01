<?php

namespace Signa\Libs;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl;
use Signa\Models\CategoryTree;
use Signa\Models\Recipes as RecipesModel;
use Signa\Models\RecipeActivity;
use Signa\Models\RecipeProduct;
use Signa\Models\RecipeCustomField;

class Recipes extends Plugin
{
    /**
     * generate new unique receipt code
     *
     * @return int
     */
    public static function generateCode()
    {
        $code = mt_rand(10000000,99999999);
        do {
            $recipe = RecipesModel::findFirstByCode($code);

            if ($recipe) {
                $code = mt_rand(10000000,99999999);
            } else {
                break;
            }
        } while (1);

        return $code;
    }

    /**
     * @param RecipesModel $recipe
     */
    public static function clearRecipeData(RecipesModel $recipe)
    {
        foreach ($recipe->RecipeActivity as $activity) {
            $activity->delete();
        }
        foreach ($recipe->RecipeProduct as $product) {
            $product->delete();
        }
        foreach ($recipe->RecipeCustomField as $field) {
            foreach ($field->Options as $option)
            {
                $option->delete();
            }
            $field->delete();
        }
    }

    /**
     * @param RecipesModel $recipe
     */
    public static function getCategories(RecipesModel $recipe)
    {
        $categories = array();
        foreach ($recipe->CategoryTree as $category) {
            $nodes = array($category->getName());
            if ($category->getParentId()) {
                $parent = CategoryTree::findFirst($category->getParentId());
                $nodes[] = $parent->getName();
                do {
                    if (!$parent->getParentId()) { break; }
                    $parent = CategoryTree::findFirst($parent->getParentId());
                    $nodes[] = $parent->getName();
                } while (1);
            }
            $categories[] = implode(' > ', array_reverse($nodes));
        }

        return $categories;
    }

    /**
     * @param RecipesModel $recipe
     */
    public static function getCategoriesIdsArr(RecipesModel $recipe)
    {
        $categories = array();
        foreach ($recipe->CategoryTree as $category) {
            $nodes = array($category->getId());
            if ($category->getParentId()) {
                $parent = CategoryTree::findFirstById($category->getParentId());
                if (!$parent) { continue; }
                $nodes[] = $parent->getId();
                do {
                    if (!$parent->getParentId()) { break; }
                    $parent = CategoryTree::findFirstById($parent->getParentId());
                    if (!$parent)  { break; } // fix for mess in DB, and elements manually removed
                    $nodes[] = $parent->getId();
                } while (1);
            }
            $categories = array_merge($categories, $nodes);
        }

        return $categories;
    }

    /**
     * @param RecipesModel $baseRecipe
     * @param string $code
     * @param string $name
     * @return mixed
     */
    public static function createLabRecipe(RecipesModel $baseRecipe, $code = null, $name = null, $labId = null)
    {
        $recipe = RecipesModel::findFirst(
            array(
                'deleted_at IS NULL AND parent_id = :parent: AND lab_id = :lab:',
                'bind' => array(
                    'parent'    => $baseRecipe->getId(),
                    'lab'       => $labId
                )
            )
        );
        if ($recipe) {
            return false;
        }

        $labRecipe = new RecipesModel();
        $labRecipe->setCode($baseRecipe->getCode());
        $labRecipe->setName($baseRecipe->getName());
        $labRecipe->setDescription($baseRecipe->getDescription());
        $labRecipe->setCustomCode($code);
        $labRecipe->setCustomName($name);
        $labRecipe->setLabId($labId);
        $labRecipe->setParentId($baseRecipe->getId());
        $labRecipe->setPriceType(1);
        $labRecipe->setStatuses($baseRecipe->getStatuses());

        return $labRecipe->save();
    }
}