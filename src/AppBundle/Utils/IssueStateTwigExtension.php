<?php
/**
 * Created by PhpStorm.
 * User: pawkrol
 * Date: 11/23/16
 * Time: 8:31 PM
 */

namespace AppBundle\Utils;


class IssueStateTwigExtension extends \Twig_Extension
{
    public function getFilters(){
        return array(
            new \Twig_SimpleFilter('issueState', array($this, 'issueStateFilter')),
        );
    }

    public function issueStateFilter($state){
        return IssueState::getName($state);
    }

    public function getName(){
        return 'issue_state_extension';
    }
}