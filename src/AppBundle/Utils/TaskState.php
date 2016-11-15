<?php
/**
 * Created by PhpStorm.
 * User: pawkrol
 * Date: 10/30/16
 * Time: 3:23 PM
 */

namespace AppBundle\Utils;


class TaskState
{
    const STATE_TODO = 0;
    const STATE_PROGRESS = 1;
    const STATE_DONE = 2;

    protected $state = self::STATE_TODO;
}