<?php

namespace Signa\Libs;

use Signa\Models\Logs;
use Signa\Models\Organisations;
use Signa\Models\OrganisationTypes;
use Signa\Models\Products;
use Signa\Models\Purchase;
use Signa\Models\Users;

/**
 * Class with log functions to read and create logs in MongoDB
 *
 */
class MongoLogger
{
    private $profiler;
    private $prologger;

    public function __construct($profiler = null)
    {
//        $this->profiler = $profiler;
//        $this->prologger =  new \Fabfuel\Prophiler\Adapter\Psr\Log\Logger($profiler);
    }


    public function readLog(array $params)
    {
//        return false;
        /*
         *  Sample use of params search array
         *             array(
         *             "conditions" => array(
         *                  "user" => "max@sano-net.pl"
         *          )
         *      )
         */
        $logs = Logs::find($params);
        return $logs;
    }

    public function createLog(array $params, $user)
    {
//        return false;
        $log = new Logs();
        foreach ($params as $key => $param) {
            $log->$key = $param;
        }
        $log->user = $user;

        if ($log->save() == false) {
//            $this->prologger->debug('Mongo log error!', ['params' => $params, 'errors' => $log->getMessages()]);
        } else {
//            $this->prologger->debug('Mongo log succesfull!', ['params' => $params]);
        }
    }

    public function createImportedProducts(array $prod, $cronlog = false)
    {
        if ($prod) {
            $products = Products::find([
                'id IN ({letter:array})',
                'bind' => [
                    'letter' => $prod,
                ],
            ]);

            if ($cronlog) {
                echo "\n" . sprintf(':: PURCHASE :: found %s products', count($products));
            }

            $c = 0;
            foreach ($products as $p) {
                $c++;
                $oldOne = Purchase::find([
                    'conditions' => [
                        'details.id' => (int)$p->getId(),
                    ],
                ]);
                foreach ($oldOne as $oo) {
                    if ($oo->delete() === false) {
                        echo "Sorry, we can't delete the oo right now: \n";

                        $messages = $oo->getMessages();

                        foreach ($messages as $message) {
                            echo $message, "\n";
                        }
                    } else {
//                        echo 'The robot was deleted successfully!';
                    }
                }

                if ($cronlog) {
                    echo "\n" . sprintf(':: PURCHASE :: [%s / %s] :: looking for old product in Purchase', $c, count($products));
                }
            }

            if ($cronlog) {
                echo "\n" . sprintf(':: PURCHASE :: looking for LAB organisations...');
            }
            $organisations = Organisations::find();
            //create mongo products for every LAB user
            $labOrganisations = [];
            /** @var Organisations $organisation */
            foreach ($organisations as $organisation) {
                $organisationId = $organisation->getId();
                if ($organisation) {
                    //check if user is lab user
                    if ($organisation->OrganisationType->getSlug() == 'lab') {
                        $labOrganisations[] = $organisation;
                    }
                }
            }

            if ($cronlog) {
                echo "\n" . sprintf(':: PURCHASE :: found: %s LAB organisations', count($labOrganisations));
            }

            $cc = 0;
            foreach ($labOrganisations as $organisation) {
                $c = 0;
                $cc++;
                foreach ($products as $key => $product) {
                    $c++;
                    $purchase = new Purchase();
                    foreach ($product->toArray() as $key => $row) {

                        $var = $this->convertFields($row);

                        $purchase->details->$key = $var;
                    }
                    $purchase->productid = (int)$product->getId();
                    $purchase->startdate = $product->getStartDate();
                    $purchase->fororganisation = $organisation->getId();
                    $purchase->type = 'product';
                    $purchase->isopened = false;
                    $purchase->supplierName = $product->Organisation->getName();

                    if ($purchase->save() == false) {

//                       $this->prologger->debug('Mongo log error!', ['params' => $params, 'errors' => $log->getMessages()]);
                    } else {
//                       $this->prologger->debug('Mongo log succesfull!', ['params' => $params]);
                    }

                    if ($cronlog) {
                        echo "\n" . sprintf(':: PURCHASE :: prod: [%s / %s] lab: [%s / %s] :: save for Lab: %s', $c, count($products), $cc, count($labOrganisations), $organisation->getName());
                    }
                }
            }
        }
    }

    private function convertFields($row){
        $j = 'string';
        if (is_numeric($row)) {
            $j = 'num';
        } else if (is_null($row)) {
            $j = 'null';
        }

        switch ($j) {
            case 'num';
                $var = (int)$row;
                break;
            case 'null';
                $var = null;
                break;
            case 'string';
                $var = (string)$row;
                break;
            default;
                $var = $row;
                break;

        }

        return $var;
    }

}

