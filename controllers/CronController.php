<?php

namespace Signa\Controllers;

use Signa\Models\Products;

class CronController extends ControllerBase
{
    /*
     * Hashed action activate-imported-products based of function
     * in initialize in CrontrollerBase
     */
    public function zgzkogfjoteyjdmmmexntayzaxmdyowyAction()
    {
        $productsToActivate = Products::find('approved = 1 AND declined = 0 AND active = 0 AND start_date >= \''.date('Y-m-d').'\'');
        $approved = 0;
        $deactivated = 0;
        $oldProductsArr = array();

        foreach ($productsToActivate as $product){

            $oldProductId = $product->getOldProductId();

            if(!is_null($oldProductId)){

                $oldProductsArr[] = Products::findFirst($oldProductId);
            }
            $product->approveWithoutCategory();

            if(count($product->getMessages()) === 0){
                $approved++;
            }
        }

        foreach ($oldProductsArr as $oldProduct){

            $oldProduct->softDelete();
            $deactivated++;
        }
        return json_encode(array('status' => true, 'approved' => $approved, 'deactivated' => $deactivated));
    }
}
