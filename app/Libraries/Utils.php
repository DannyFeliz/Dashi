<?php
/**
 * Created by PhpStorm.
 * User: Danny
 * Date: 24/09/2017
 * Time: 08:47 PM
 */

namespace App\Libraries;


class Utils
{
    /**
     * Extracts all usernames from a text
     *
     * @param $text
     * @return array
     *
     * @example 'Please @jhon fix this' -> 'jhon'
     */
    public static function extractUsernames($text)
    {
        preg_match_all('/(\s|^)@([^\s\.]+)/', $text, $matches);

        return $matches[2];
    }
}