<?php

namespace Signa\Controllers\Lab;

class InitController extends \Signa\Controllers\ControllerBase
{
    public $user;

    public function initialize()
	{
        parent::initialize();
        $this->user = $this->session->get('auth');
	}
}
