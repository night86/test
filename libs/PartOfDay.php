<?php

namespace Signa\Libs;

use Phalcon\Events\Event,
        Phalcon\Mvc\User\Plugin,
        Phalcon\Mvc\Dispatcher,
        Phalcon\Acl;

class PartOfDay extends Plugin
{
    /**
     * get current part of day
     * @return string
     */
    public function getCurrentPartOfDay() {

        /* This sets the $time variable to the current hour in the 24 hour clock format */
        $time = date("H");
        /* Set the $timezone variable to become the current timezone */
        $timezone = date("e");
        /* If the time is less than 1200 hours, show good morning */
        if ($time < "12") {
            return "Good morning";
        } else if ($time >= "12" && $time < "17") {
            return "Good afternoon";
        } else if ($time >= "17" && $time < "19") {
            return "Good evening";
        } else if ($time >= "19") {
            return "Good night";
        }
    }
}