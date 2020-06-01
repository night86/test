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
use Signa\Models\DentistOrder as DentistOrderModel;

class DentistOrders extends Plugin
{
    /**
     * generate new unique receipt code
     *
     * @return int
     */
    public static function generateCode()
    {
        $code = mt_rand(100000,999999);
        do {
            $recipe = DentistOrderModel::findFirstByCode($code);

            if ($recipe) {
                $code = mt_rand(100000,999999);
            } else {
                break;
            }
        } while (1);

        return $code;
    }

    /**
     * @param int $labId
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function getOrdersIncomingByLab($labId)
    {
        $orders = DentistOrderModel::query()
            ->join('Signa\Models\DentistOrderRecipe', 'dor.order_id = Signa\Models\DentistOrder.id', 'dor')
            ->join('Signa\Models\Recipes', 'r.id = dor.recipe_id', 'r')
            ->where("r.lab_id = :labid:")
            ->andWhere('Signa\Models\DentistOrder.status = 2')
            ->bind(array(
                "labid" => $labId
            ))
            ->groupBy('Signa\Models\DentistOrder.id')
            ->execute();

        return $orders;
    }

    /**
     * @param int $labId
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function getOrdersConfirmedByLab($labId)
    {
        $orders = DentistOrderModel::query()
            ->join('Signa\Models\DentistOrderRecipe', 'dor.order_id = Signa\Models\DentistOrder.id', 'dor')
            ->join('Signa\Models\Recipes', 'r.id = dor.recipe_id', 'r')
            ->where("r.lab_id = :labid:")
            ->andWhere('Signa\Models\DentistOrder.status = 3')
            ->bind(array(
                "labid" => $labId
            ))
            ->groupBy('Signa\Models\DentistOrder.id')
            ->execute();

        return $orders;
    }

    /**
     * @param int $labId
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function getOrdersByLab($labId)
    {
        $orders = DentistOrderModel::query()
            ->join('Signa\Models\DentistOrderRecipe', 'dor.order_id = Signa\Models\DentistOrder.id', 'dor')
            ->join('Signa\Models\Recipes', 'r.id = dor.recipe_id', 'r')
            ->where("r.lab_id = :labid:")
            ->andWhere('Signa\Models\DentistOrder.status > 1')
            ->bind(array(
                "labid" => $labId
            ))
            ->groupBy('Signa\Models\DentistOrder.id')
            ->execute();

        return $orders;
    }

    /**
     * @param int $labId
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function getOrdersInDeliveryByLab($labId)
    {
        $orders = DentistOrderModel::query()
            ->join('Signa\Models\DentistOrderRecipe', 'dor.order_id = Signa\Models\DentistOrder.id', 'dor')
            ->join('Signa\Models\Recipes', 'r.id = dor.recipe_id', 'r')
            ->where("r.lab_id = :labid:")
            ->andWhere('Signa\Models\DentistOrder.status = 4')
            ->bind(array(
                "labid" => $labId
            ))
            ->groupBy('Signa\Models\DentistOrder.id')
            ->execute();

        return $orders;
    }

    /**
     * @param int $labId
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function getOrdersDeliveredByLab($labId)
    {
        $orders = DentistOrderModel::query()
            ->join('Signa\Models\DentistOrderRecipe', 'dor.order_id = Signa\Models\DentistOrder.id', 'dor')
            ->join('Signa\Models\Recipes', 'r.id = dor.recipe_id', 'r')
            ->where("r.lab_id = :labid:")
            ->andWhere('Signa\Models\DentistOrder.status = 5')
            ->bind(array(
                "labid" => $labId
            ))
            ->groupBy('Signa\Models\DentistOrder.id')
            ->execute();

        return $orders;
    }
}