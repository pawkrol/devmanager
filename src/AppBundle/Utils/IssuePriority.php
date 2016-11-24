<?php
/**
 * Created by PhpStorm.
 * User: pawkrol
 * Date: 11/23/16
 * Time: 9:37 PM
 */

namespace AppBundle\Utils;


class IssuePriority
{
    const LOW = -1;
    const NORMAL = 0;
    const HIGH = 1;

    protected $state = self::NORMAL;

    static function getName($priority){
        switch ($priority){
            case self::LOW:
                return "LOW";

            case self::NORMAL:
                return "NORMAL";

            case self::HIGH:
                return "HIGH";
        }
    }
}