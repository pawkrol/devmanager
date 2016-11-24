<?php
/**
 * Created by PhpStorm.
 * User: pawkrol
 * Date: 11/23/16
 * Time: 8:31 PM
 */

namespace AppBundle\Utils;


class IssuePriorityTwigExtension extends \Twig_Extension
{
    public function getFilters(){
        return array(
            new \Twig_SimpleFilter('issuePriority', array($this, 'issuePriorityFilter')),
        );
    }

    public function issuePriorityFilter($priority){
        return IssuePriority::getName($priority);
    }

    public function getName(){
        return 'issue_priority_extension';
    }
}