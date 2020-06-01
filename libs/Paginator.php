<?php

namespace Signa\Libs;

use Phalcon\Mvc\User\Plugin;
use Phalcon\DI;

class Paginator extends Plugin
{
    public static function getPageUrl($pageNumber)
    {
        /* @var \Phalcon\Http\Request|\Phalcon\Http\RequestInterface $request */
        $request = Di::getDefault()->get('request');
        $query = $request->getQuery();
        $query['page'] = $pageNumber;

        // remove system values
        foreach ($query as $k => $v) {
            if (preg_match('/_/', $k)) {
                unset($query[$k]);
            }
        }

        return http_build_query($query);
    }
}