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
use AppBundle\Utils\IssueState;
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
        $owner = $em->getRepository("AppBundle:ProjectUser")->findOneBy(["user" => $this->getUser()])->getOwner();

        $issueForm = $this->createForm(IssueEditType::class, $issue);
        $issueForm->handleRequest($request);
        if ($issueForm->isSubmitted() && $issueForm->isValid()){

            $em->flush();
        }

        $assignedUserForm = $this->handleAssignedUserIdForm($request, $em, $issue, $projectId);
        $assignedUserForm->handleRequest($request);
        if ($assignedUserForm->isSubmitted() && $assignedUserForm->isValid()){
            $data = $assignedUserForm->getData();
            $user = $em->getRepository("AppBundle:User")->findOneBy(["id" => $data["userId"]]);

            $issue->setAssignedUserId($user);

            $em->flush();

            return $this->redirectToRoute("issue", ["projectId" => $projectId, "id" => $id]);
        }

        return $this->render('issue.html.twig', ["issueForm" => $issueForm->createView(),
                                                    "auForm" => $assignedUserForm->createView(), "owner" => $owner,
                                                    "issue" => $issue, "project" => $project,
                                                    "issuedUser" => $issuedUser->getUsername(),
                                                    "assignedUser" => $assignedUser->getUsername()]);
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

    private function handleAssignedUserIdForm(Request $request, $em, $issue, $projectId){
        $users = $em->getRepository("AppBundle:ProjectUser")->getProjectUsers($projectId);

        $usernameIdPairs = array();
        foreach ($users as $u){
            array_push($usernameIdPairs, array(
                $u->getUsername() => $u->getId()
            ));
        }

        $assignedUserId = array();
        $assignedUserForm = $this->createFormBuilder($assignedUserId)
                                ->add('userId', ChoiceType::class, [
                                    'choices' => $usernameIdPairs,
                                    'group_by' => function($category, $key, $index){
                                        return null;
                                    },
                                    'required' => true,
                                ])->getForm();

        return $assignedUserForm;
    }
}