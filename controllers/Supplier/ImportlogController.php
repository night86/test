<?php

namespace Signa\Controllers\Supplier;

use Signa\Models\ImportProducts;

class ImportlogController extends InitController
{
    public function initialize(){

        $this->view->disableSubnav = true;
        parent::initialize();
    }

    public function indexAction(){

        $this->response->redirect('/supplier/import/log');
        return;
    }

    public function viewAction($id){

        $import = ImportProducts::findFirst($id);
        $products = $import->Products;

        $this->view->import = $import;
        $this->view->products = $products;
    }
}
