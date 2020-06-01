<?php

namespace Signa\Controllers\Lab;

use Signa\Models\LogLabOrderStatus;
use Signa\Models\LogLabPriceChange;
use Signa\Models\OrderCart;
use Signa\Models\OrderShortlist;
use Signa\Models\Products;
use Signa\Models\Purchase;
use Signa\Libs\DentistOrders;

class IndexController extends InitController
{
    public function indexAction(){

        $this->view->disableSubnav = true;

        $this->assets->collection('footer')
            ->addJs("js/app/lab/dashboard.js");
    }

    public function dashboardAction(){

        session_write_close();
        $organisationId = (int)$this->currentUser->Organisation->getId();
        $products = Purchase::find([
            'conditions' => [
                'fororganisation' => $organisationId,
                'startdate' => ['$lte' => date("Y-m-d")],
                'type' => 'product',
                'isopened' => false
            ],
            'limit' => 5
        ]);
        $productsCount = Purchase::count([
            'conditions' => [
                'fororganisation' => $organisationId,
                'startdate' => ['$lte' => date("Y-m-d")],
                'type' => 'product',
                'isopened' => false
            ]
        ]);

        $neworders = DentistOrders::getOrdersIncomingByLab($this->currentUser->getOrganisationId());
        $mproducts = $products;
        $mproductsCount = $productsCount;
        $productsCount = $this->productAlertsCount();
        $products = $this->productAlerts(5);
        $userslog = $this->mongoLogger->readLog(
            [
                'conditions' => [
                    'action' => 'login',
                    'organisation_id' => $this->currentUser->getOrganisationId(),
                    'isopened' => false
                ],
                "sort" => ["created_at" => -1],
            ]
        );
        $status = LogLabOrderStatus::find(
            [
                'conditions' => [
                    'order_organisation_id' => $this->currentUser->getOrganisationId(),
                    'isopened' => false
                ],
                "sort" => ["created_at" => -1],
            ]
        );
        $statusesNames = OrderCart::getStatusArray();
        $newShortlist = OrderShortlist::find(
            [
                'organisation_id = :organisation_id: AND isopened IS NULL ORDER BY created_at DESC',
                'bind' => [
                    'organisation_id' => $this->currentUser->getOrganisationId()
                ]
            ]
        );
        $currentQuery = $this->request->getQuery();

        $currentUser = $this->view->currentUser;

        $dashboardHtml = $this->simpleView->render('lab/index/_dashboard', [
            'neworders' => $neworders,
            'mproducts' => $mproducts,
            'mproductsCount' => $mproductsCount,
            'productsCount' => $productsCount,
            'products' => $products,
            'userslog' => $userslog,
            'status' => $status,
            'statusesNames' => $statusesNames,
            'newShortlist' => $newShortlist,
            'curretQuery' => $currentQuery,
            'currentUser' => $currentUser
        ]);
        echo $dashboardHtml; die;
    }

    public function startAction(){

    }

    public function newProductsAction(){

        $organisationId = (int)$this->currentUser->Organisation->getId();
        $products = Purchase::find([
            'conditions' => [
                'fororganisation' => $organisationId,
                'startdate' => ['$lte' => date("Y-m-d")],
                'type' => 'product',
                'isopened' => false
            ],
        ]);

        foreach ($products as $product) {

            $product->isopened = true;
            $product->save();
        }
        $this->view->mproducts = $products;
        $this->view->disableSubnav = true;
    }

    public function priceAlertsAction(){

        $products = $this->productAlerts();

        foreach ($products as $product) {

            $product->isopened = true;
            $product->save();
        }
        $this->view->products = $products;
        $this->view->disableSubnav = true;
    }

    private function productAlertsCount(){

        return LogLabPriceChange::count([
            'conditions' => [
                'organisation_id' => $this->currentUser->getOrganisationId(),
                'isopened' => false,
                'start_date' => ['$lte' => date("Y-m-d")],
            ]
        ]);
    }

    private function productAlerts($limit = null){

        $rules = [
            'conditions' => [
                'organisation_id' => $this->currentUser->getOrganisationId(),
                'isopened' => false,
                'start_date' => ['$lte' => date("Y-m-d")],
            ],
            "sort" => ["created_at" => -1],
        ];

        if ($limit) {
            $rules['limit'] = $limit;
        }

        return LogLabPriceChange::find(
            $rules
        );
    }
}
