<?php
/**
 * Created by PhpStorm.
 * User: pawkrol
 * Date: 11/30/16
 * Time: 5:18 PM
 */

namespace AppBundle\Controller;


use AppBundle\Utils\IssueState;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class MembersController extends Controller
{
    /**
     * @Route("/project/members", name="members_page")
     */
    function usersAction(Request $request){
        $projectId = $request->query->get('projectId');

        $em = $this->getDoctrine()->getManager();

        $project = $em->getRepository("AppBundle:Project")->findOneBy(["id" => $projectId]);
        $projectUsers = $em->getRepository("AppBundle:ProjectUser")->findBy(["project" => $project]);
        $projectOwner = $em->getRepository("AppBundle:ProjectUser")->findOneBy(["project" => $project,
            "user" => $this->getUser(), "owner" => true]);

        return $this->render("members.html.twig", ["project" => $project,
                                                        "projectUsers" => $projectUsers,
                                                        "projectOwner" => $projectOwner]);
    }

    /**
     * @Route("/project/members/remove", name="remove-member")
     */
    function removeUserAction(Request $request){
        $userId = $request->query->get('userId');
        $projectId = $request->query->get('projectId');

        $em = $this->getDoctrine()->getManager();

        $project = $em->getRepository("AppBundle:Project")->findOneBy(["id" => $projectId]);
        $user = $em->getRepository("AppBundle:User")->findOneBy(["id" => $userId]);
        $projectOwners = $em->getRepository("AppBundle:ProjectUser")->findBy(["project" => $project,
            "owner" => true]);

        $owner = false;
        $toDeleteOwner = false;
        foreach ($projectOwners as $projectOwner){
            if ($projectOwner->getUser() === $this->getUser()){
                $owner = true;
            }
            if ($projectOwner->getUser() === $user){
                $toDeleteOwner = true;
            }
        }

        if (!$owner){
            return new Response("not owner", 400);
        }

        if ($toDeleteOwner && count($projectOwners) == 1){
            return new Response("Last owner, cannot delete", 400);
        }

        $projectUser = $em->getRepository("AppBundle:ProjectUser")->findOneBy(["project" => $project,
            "user" => $user]);

        if (!$projectUser){
            return new Response("could not find user", 400);
        }

        $tasks = $em->getRepository("AppBundle:Task")->findBy(["projectId" => $project,
            "userId" => $user]);

        $assignedIssues = $em->getRepository("AppBundle:Issue")->findBy(["projectId" => $project,
            "assignedUserId" => $user]);

        foreach ($assignedIssues as $issue){
            $issue->setAssignedUserId(null);
            $issue->setState(IssueState::CONF);
        }

        foreach ($tasks as $task){
            $em->remove($task);
        }

        $em->remove($projectUser);
        $em->flush();

        return new Response("ok", 200);
    }
}