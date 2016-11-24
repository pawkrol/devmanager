<?php
/**
 * Created by PhpStorm.
 * User: pawkrol
 * Date: 11/3/16
 * Time: 7:33 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\ProjectUser;
use AppBundle\Utils\TaskState;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/ajax");
 */
class AjaxController extends Controller
{
    /**
     * @Route("/role")
     */
    public function getRole(Request $request){
        $id = $request->query->get('id');

        $projectUser = $this->getProjectUserEntityById($id);
        if ($projectUser == null){
            return new JsonResponse(array('error' => 'not found'), $status = 400);
        }

        $data = array("role" => $projectUser->getProjectRole(), "owner" => $projectUser->getOwner());
        return $this->json($data, $status = 200, $headers = array(), $context = array());
    }

    /**
     * @Route("/tasks")
     */
    public function getTasks(Request $request){
        $projectId = $request->query->get("projectId");
        $template = $this->forward('AppBundle:Task:getTasks', array("projectId" => $projectId))->getContent();
        $json = json_encode($template);

        $response = new Response($json, 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/tasks/set")
     */
    public function setTasks(Request $request){
        $taskId = $request->query->get("taskId");
        $taskState = $request->query->get("state");

        if ($taskId === null || $taskState === null){
            return new Response("insufficient data", 400);
        }

        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('AppBundle:Task')->find($taskId);

        if (!$task){
            return new Response("task does not exists", 400);
        }

        $task->setState($this->evaluateTaskStatus($taskState));
        $em->flush();

        return new Response("ok", 200);
    }

    /**
     * @Route("/tasks/delete")
     */
    public function deleteTasks(Request $request){
        $taskId = $request->query->get("taskId");

        if ($taskId === null){
            return new Response("insufficient data", 400);
        }

        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('AppBundle:Task')->find($taskId);

        if (!$task){
            return new Response("task does not exists", 400);
        }

        $em->remove($task);
        $em->flush();

        return new Response("ok", 200);
    }

    /**
     * @Route("/projects/delete")
     */
    public function deleteProjects(Request $request){
        $projectId = $request->query->get("projectId");

        if ($projectId === null){
            return new Response("insufficient data", 400);
        }

        $em = $this->getDoctrine()->getManager();
        $isOwner = $em->getRepository('AppBundle:ProjectUser')->isUserOwner(
            $this->getUser(), $projectId
        );

        if ($isOwner === null){
            return new Response("not owner", 400);
        }

        $project = $em->getRepository('AppBundle:Project')->findOneBy(["id" => $projectId]);

        $em->remove($project);
        $em->flush();

        return new Response("ok", 200);
    }

    /**
     * @Route("/issues/get/assigned")
     */
    public function getAssignedIssues(Request $request){
        $projectId = $request->query->get("projectId");

        if ($projectId === null){
            return new Response("insufficient data", 400);
        }

        $template = $this->forward('AppBundle:Issue:getAssignedIssues', array("projectId" => $projectId))->getContent();
        $json = json_encode($template);

        $response = new Response($json, 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function evaluateTaskStatus($string_name){
        if (strcmp("todos", $string_name) === 0){
            return TaskState::STATE_TODO;
        } else if (strcmp("inprogresses", $string_name) === 0){
            return TaskState::STATE_PROGRESS;
        } else if (strcmp("dones", $string_name) === 0){
            return TaskState::STATE_DONE;
        }

        return -1;
    }

    private function getProjectUserEntityById($id){
        $user = $this->getUser();

        $projectUserRepository = $this->getDoctrine()->getRepository('AppBundle:ProjectUser');

        return $projectUserRepository->findOneBy(array('user' => $user, 'project' => $id));
    }
}