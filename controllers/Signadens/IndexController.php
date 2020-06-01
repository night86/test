<?php

namespace Signa\Controllers\Signadens;

use Signa\Models\ImportProducts;
use Signa\Models\Logs;
use Signa\Models\Purchase;

class IndexController extends InitController
{
    public function indexAction(){

        $this->view->imports = ImportProducts::find('closed = 0');
        $this->view->disableSubnav = true;
        $this->view->logs = $this->mongoLogger->readLog(
            array('conditions' => array(
                'action' => 'login'
            ))
        );
    }

    public function startAction(){

    }
}
