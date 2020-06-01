<?php

namespace Signa\Controllers\Supplier;

class InitController extends \Signa\Controllers\ControllerBase
{
    public $user;

    public function initialize(){

        parent::initialize();
        $this->user = $this->session->get('auth');
	}
}
