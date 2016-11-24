<?php
/**
 * Created by PhpStorm.
 * User: pawkrol
 * Date: 11/23/16
 * Time: 2:33 PM
 */

namespace AppBundle\Utils;


class IssueState
{
    const NEWBE = 0;
    const ACK = 1;
    const CONF = 2;
    const ASSIGNED = 3;
    const CLOSED = 4;

    protected $state = self::NEWBE;

    static function getName($number){
        switch ($number){
            case self::NEWBE:
                return "NEW";

            case self::ACK:
                return "ACKNOWLEDGED";

            case self::CONF:
                return "CONFIRMED";

            case self::ASSIGNED:
                return "ASSIGNED";

            case self::CLOSED:
                return "CLOSED";
        }
    }
}