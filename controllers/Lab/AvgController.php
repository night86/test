<?php

namespace Signa\Controllers\Lab;

use Phalcon\Crypt;
use Signa\Libs\Encryption;

class AvgController extends InitController
{
    public function indexAction()
    {
        $this->view->disableSubnav = true;
        $this->view->isIframe = true;

        $this->view->styling = base64_encode(json_encode([
            'backgroundColor' => '#f7f4e5',
            'styleColor' => '#000',
            'logoURL' => 'https://mijn. /uploads/files/signadens-logo.png'
        ]));

        $crypt = new Encryption();

        $this->view->username = $crypt->encrypt(
            $this->currentUser->Organisation->getIso2hUsername(),
            $this->config->security->secret
        );

        $this->view->password = $crypt->encrypt(
            $this->currentUser->Organisation->getIso2hPassword(),
            $this->config->security->secret
        );

        $this->assets->collection('additional')
            ->addJs('js/app/avg.js');
    }
}
