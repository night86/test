<?php

namespace Signa\Controllers;

class IndexController extends ControllerBase
{
    public function indexAction(){

        $this->response->redirect("auth/login");
    }

    public function supportAction(){

    }
}
