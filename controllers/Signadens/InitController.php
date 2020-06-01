<?php

namespace Signa\Controllers\Signadens;

class InitController extends \Signa\Controllers\ControllerBase
{
    /**
     * @var \Signa\Models\Users
     */
    public $user;

	public function initialize()
	{
	    parent::initialize();
        $this->user = $this->session->get('auth');
	}
}
