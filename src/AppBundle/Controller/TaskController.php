<?php
/**
 * Created by PhpStorm.
 * User: pawkrol
 * Date: 11/11/16
 * Time: 2:38 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Task;
use AppBundle\Utils\TaskState;
use AppBundle\Utils\TaskType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    /**
     * @Route("/dashboard/task");
     */
    function getTaskBodyAction(Request $request){
        $projectId = $request->query->get("projectId");
        $user = $this->getUser();

        $em = $this->getDoctrine()->getRepository('AppBundle:Project');
        $project = $em->getProjectById($projectId);

        $em = $this->getDoctrine()->getRepository('AppBundle:ProjectUser');
        $users = $em->getProjectUsers($projectId);

//        if (!in_array($user, $users))
//            return new Response("NOT MEMBER", 400);

        $usernameIdPairs = array();
        foreach ($users as $u){
            array_push($usernameIdPairs, array(
                $u->getUsername() => $u->getId()
            ));
        }

        $form = $this->handleForm($request, $project, $usernameIdPairs);

        return $this->render('task.html.twig', array("project" => $project, "form" => $form->createView()));
    }

    function getTasksAction($projectId){
        $em = $this->getDoctrine()->getRepository('AppBundle:ProjectUser');
        $member = $em->findOneBy(["user" => $this->getUser()]);

        if ($member === null){
            return new Response("not member", 400);
        }

        $owner = $member->getOwner();

        $em = $this->getDoctrine()->getRepository('AppBundle:Task');
        $tasks = null;

        if ($owner) {
            $tasks = $em->findBy(["projectId" => $projectId]);
        } else {
            $tasks = $em->findBy(["projectId" => $projectId, "userId" => $this->getUser()]);
        }

        $todos = array();
        $inprogresses = array();
        $dones = array();

        foreach ($tasks as $task){
            $state = $task->getState();

            if ($state === TaskState::STATE_TODO){
                array_push($todos, $task);
            } else if ($state === TaskState::STATE_PROGRESS){
                array_push($inprogresses, $task);
            } else {
                array_push($dones, $task);
            }
        }

        return $this->render('tasks.html.twig', array("todos" => $todos,
                                                        "inprogresses" => $inprogresses,
                                                        "dones" => $dones));
    }

    private function handleForm(Request $request, $project, $usernameIdPairs){
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task, array("usernameIdPairs" => $usernameIdPairs));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $task = $form->getData();

            $em = $this->getDoctrine()->getRepository('AppBundle:User');
            $user = $em->findOneBy(["id" => $task->getUserId()]);

            $task->setUserId($user);
            $task->setProjectId($project);
            $task->setState(TaskState::STATE_TODO);

            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            unset($task);
            unset($form);
            $task = new Task();
            $form = $this->createForm(TaskType::class, $task, array("usernameIdPairs" => $usernameIdPairs));
        }

        return $form;
    }

}