<?php

namespace Signa\Libs;

use Phalcon\Events\Event,
        Phalcon\Mvc\User\Plugin,
        Phalcon\Mvc\Dispatcher,
        Phalcon\Acl;

class Convert extends Plugin
{
    /**
     * Create array from data collection based on name and id as key
     *
     * @param mixed $collection
     * @return array
     */
    public static function toIdArray($collection)
    {
        $newArr = array();
        foreach ($collection as $object)
        {
            $newArr[$object->getId()] = $object->getName();
        }
        return $newArr;
    }
}