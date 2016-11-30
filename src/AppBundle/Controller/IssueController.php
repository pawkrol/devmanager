<?php
/**
 * Created by PhpStorm.
 * User: pawkrol
 * Date: 11/23/16
 * Time: 7:59 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Issue;
use AppBundle\Utils\IssueEditType;
use AppBundle\Utils\IssueNewType;
use AppBundle\Utils\IssueState;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;

class IssueController extends Controller
{

    /**
     * @Route("/project/{projectId}/issue/{id}", name="issue")
     */
    public function issueAction(Request $request, $id, $projectId){
        $em = $this->getDoctrine()->getManager();

        $project = $em->getRepository("AppBundle:Project")->findOneBy(["id" => $projectId]);
        $issue = $em->getRepository("AppBundle:Issue")->findOneBy(["id" => $id]);
        $issuedUser = $em->getRepository("AppBundle:User")->findOneBy(["id" => $issue->getIssuedUserId()]);
        $assignedUser = $em->getRepository("AppBundle:User")->findOneBy(["id" => $issue->getAssignedUserId()]);
        $owner = $em->getRepository("AppBundle:ProjectUser")
                        ->findOneBy(["user" => $this->getUser(), "project" => $project])
                        ->getOwner();

        $assignedUserUsername = "none";
        if ($assignedUser){
            $assignedUserUsername = $assignedUser->getUsername();
        }

        $usernameIdPairs = $this->getUsernameIdPairs($em, $projectId, $owner);
        $form = $this->createForm(IssueEditType::class, $issue, ["usernameIdPairs" => $usernameIdPairs]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            if (!$issue->getAssignedUserId()) {
                $issue->setAssignedUserId($assignedUser);
            } else {
                if ($issue->getState() != IssueState::CLOSED){
                    $issue->setState(IssueState::ASSIGNED);
                }
            }

            $em->flush();

            return $this->redirectToRoute('issue', ["projectId" => $projectId, "id" => $id]);
        }

        return $this->render('issue.html.twig', ["form" => $form->createView(),
                                                    "owner" => $owner,
                                                    "issue" => $issue, "project" => $project,
                                                    "issuedUser" => $issuedUser->getUsername(),
                                                    "assignedUser" => $assignedUserUsername]);
    }

    /**
     * @Route("/project/issues", name="issues_page")
     */
    public function issuesAction(Request $request){
        $projectId = $request->query->get("projectId");
        $em = $this->getDoctrine()->getManager();

        $project = $em->getRepository("AppBundle:Project")->findOneBy(["id" => $projectId]);
        $issues = $em->getRepository("AppBundle:Issue")->findBy(["projectId" => $projectId]);

        usort($issues, function($a, $b){
            return $a->getPriority() < $b->getPriority();
        });

        return $this->render("issues_list.html.twig", ["project" => $project, "issues" => $issues]);
    }

    /**
     * @Route("/project/{projectId}/issues/new", name="new_issue")
     */
    public function newIssueAction(Request $request, $projectId){
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository("AppBundle:Project")->findOneBy(["id" => $projectId]);
        $owner = $em->getRepository("AppBundle:ProjectUser")
            ->findOneBy(["user" => $this->getUser(), "project" => $project])
            ->getOwner();

        $usernameIdPairs = $this->getUsernameIdPairs($em, $projectId, $owner);

        $issue = new Issue();
        $form = $this->createForm(IssueNewType::class, $issue, array("usernameIdPairs" => $usernameIdPairs));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $issue = $form->getData();

            $issuedUser = $this->getUser();
            $assignedUser = $em->getRepository("AppBundle:User")->findOneBy(["id" => $issue->getAssignedUserId()]);
            $currentDate = new DateTime(date("Y-m-d H:i:s"));

            $issue->setProjectId($project);
            $issue->setIssuedUserId($issuedUser);
            $issue->setAssignedUserId($assignedUser);
            $issue->setDateAdded($currentDate);
            $issue->setState(IssueState::NEWBE);

            $em = $this->getDoctrine()->getManager();
            $em->persist($issue);
            $em->flush();

            unset($task);
            unset($form);
            $issue = new Issue();
            $form = $this->createForm(IssueNewType::class, $issue, array("usernameIdPairs" => $usernameIdPairs));
        }

        return $this->render("new_issue.html.twig", ["project" => $project, "form" => $form->createView()]);
    }

    public function getAssignedIssuesAction($projectId){
        $em = $this->getDoctrine()->getManager();
        $issues = $em->getRepository("AppBundle:Issue")->findBy([
            "assignedUserId" => $this->getUser(),
            "projectId" => $projectId,
            "state" => IssueState::ASSIGNED
        ]);

        return $this->render('assigned_issues.html.twig', ["issues" => $issues]);
    }

    private function getUsernameIdPairs($em, $projectId, $owner){
        $users = $em->getRepository("AppBundle:ProjectUser")->getProjectUsers($projectId);

        $usernameIdPairs = array();
        array_push($usernameIdPairs, array(
            "none" => null
        ));

        if ($owner) {
            foreach ($users as $u) {
                array_push($usernameIdPairs, array(
                    $u->getUsername() => $u
                ));
            }
        }

        return $usernameIdPairs;
    }

}