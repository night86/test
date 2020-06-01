<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 19.07.2016
 * Time: 11:11
 */

namespace Signa\Helpers;


class Date
{
    public static function currentDatetime()
    {
        $dt = new \DateTime();
        return $dt->format('Y-m-d H:i:s');
    }

    public static function currentDate()
    {
        $dt = new \DateTime();
        return $dt->format('Y-m-d');
    }

    public static function formatToDefault($datetime)
    {
        $dt = new \DateTime($datetime);
        return $dt->format('d-m-Y H:i');
    }

    public static function dateTimeToArr($datetime)
    {
        $dt = new \DateTime($datetime);
        return array(
            'date' => $dt->format('Y-m-d'),
            'time' => $dt->format('H:i:s')
        );
    }

    public static function makeDate($date)
    {
        $arrDatetime = explode(' ', $date);
        $datetimeObject = new \DateTime($date);
        if(count($arrDatetime) > 1){
            return $datetimeObject->format('Y-m-d H:i');
        }
        return $datetimeObject->format('Y-m-d');
    }

    public static function makeDateEU($date)
    {
        $arrDatetime = explode(' ', $date);
        $datetimeObject = new \DateTime($date);
        if(count($arrDatetime) > 1){
            return $datetimeObject->format('d-m-Y H:i');
        }
        return $datetimeObject->format('d-m-Y');
    }
}