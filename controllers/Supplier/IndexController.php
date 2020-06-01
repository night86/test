<?php

namespace Signa\Controllers\Supplier;

use Signa\Models\ImportProducts;
use Signa\Models\Notifications;
use Signa\Models\OrderCart;
use Signa\Models\Users;

class IndexController extends InitController
{
    public function indexAction(){

        $this->view->userslog = $this->mongoLogger->readLog(
            [
                'conditions' => [
                    'action' => 'login',
                    'organisation_id' => $this->currentUser->getOrganisationId(),
                    'isopened' => false
                ],
                "sort" => ["created_at" => -1],
            ]
        );

        $this->view->importsCount = ImportProducts::count([
            'supplier_id = :organisation: AND isopened IS NULL',
            'bind' => [
                'organisation' => $this->currentUser->getOrganisationId()
            ]
        ]);

        $this->view->imports = $this->currentUser->Organisation->getImports(['order' => 'created_at DESC', 'limit' => 5]);

        $this->view->notifications = Notifications::find([
            'organisation_to = :organisation: AND type = :type: AND read_at IS NULL',
            'bind' => [
                'organisation' => $this->currentUser->getOrganisationId(),
                'type' => 2 // import type
            ],
            'order' => 'created_at DESC',
            "group" => "created_at"
        ]);

        $this->view->orders = OrderCart::find([
            'deleted_at IS NULL AND status = :status: AND supplier_id = :supplier_id: AND isopened IS NULL',
            'bind' => [
                'supplier_id' => $this->currentUser->getOrganisationId(),
                'status' => 2 // confirmed order
            ],
            'order' => 'created_at DESC'
        ]);

        $this->view->disableSubnav = true;
    }

    public function startAction(){

    }
}
