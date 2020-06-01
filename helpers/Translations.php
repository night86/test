<?php

namespace Signa\Helpers;
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 13.07.2016
 * Time: 10:01
 * Help translate text as parameter in filters
 */
class Translations
{
    public static function make($text, $locale = 'nl_NL.UTF-8')
    {
        /*
         * Settings for translations
         */
//        return $locale.'aa';
//        $locale = 'en_GB.utf8';
        $locale = 'nl_NL.UTF-8';

        putenv("LC_ALL=$locale");
        setlocale(LC_ALL, $locale);
        bindtextdomain('signadens', "../app/translations/");
        bind_textdomain_codeset('signadens', 'UTF-8');
        textdomain("signadens");

        return gettext($text);
    }
}