<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 19.07.2016
 * Time: 11:11
 */

namespace Signa\Helpers;


class View
{
    public static function truncate($string, $limit = 30, $break=".", $pad="...")
    {
        // return with no change if string is shorter than $limit
        if(strlen($string) <= $limit) return $string;

        // is $break present between $limit and the end of the string?
        if(false !== ($breakpoint = strpos($string, $break, $limit))) {
            if($breakpoint < strlen($string) - 1) {
                $string = substr($string, 0, $breakpoint) . $pad;
            }
        }
        return $string;
    }

    public static function decodeString($string, $count = 10)
    {
        $decoded = html_entity_decode($string);
        return implode(' ', array_slice(explode(' ', $decoded), 0, $count)).' ...';
    }

    public static function image($argString)
    {
        $argArr = explode(', ', $argString);
        $imagePathArr = array(
            'categoryTree' => '/uploads/images/category_tree/',
            'product' => '/uploads/images/products/',
            'recipe' => '/uploads/images/recipes/',
            'organisation' => '/uploads/images/organisation/'
        );

        if(is_array($argArr))
        {
            $index = str_replace('\'', '', $argArr[0]);
            return $imagePathArr[$index].$argArr[1];
        }
        return $argString;
    }

    /**
     * @param string|array $images
     * @return string
     */
    public static function productImage($images)
    {
        if (is_string($images)) {
            $images = unserialize($images);
        }
        if (empty($images) || !isset($images[0]['url'])) {
            $image = 'http://placehold.it/600x350/ffffff?text=Geen+foto+beschikbaar';
        } else {
            $image = preg_replace('/\s+/', '%20', $images[0]['url']);
        }

        return $image;
    }
}