<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProjectUser;
use AppBundle\Entity\Share;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShareController extends Controller
{
    /**
     * @Route("/share/get_token")
     */
    public function getTokenAction(Request $request)
    {
        $projectId = $request->query->get('id');
        $role = $request->query->get('role');
        $owner = $request->query->get('owner');

        if ($projectId === null || $role === null){
            return new JsonResponse(array('error' => 'wrong data'), $status = 400);
        }

        $baseToken = base64_encode(random_bytes(12));
        $token = $this->clean($baseToken);

        $user = $this->getUser();

        $projectUserRepository = $this->getDoctrine()->getRepository('AppBundle:ProjectUser');
        $userProject = $projectUserRepository->findOneBy(array('user' => $user, 'project' => $projectId));

        if ($userProject === null){
            return new JsonResponse(array('error' => 'no such project'), $status = 400);
        } else if (!$userProject->getOwner()){
            return new JsonResponse(array('error' => 'not owner'), $status = 400);
        }

        $share = new Share();
        $share->setProject($userProject->getProject());
        $share->setRole($role);
        $share->setToken($token);
        $share->setOwner($owner);

        $em = $this->getDoctrine()->getManager();
        $em->persist($share);
        $em->flush();

        $response = array('token' => $token);
        return $this->json($response, $status = 200, $headers = array(), $context = array());
    }

    /**
     * @Route("/share")
     */
    public function shareAction(Request $request){
        $token = $request->query->get('token');

        $shareRepository = $this->getDoctrine()->getRepository("AppBundle:Share");
        $share = $shareRepository->findOneBy(array("token" => $token));

        if ($share === null){
            return new JsonResponse(array('error' => 'invalid token'), $status = 400);
        }

        $projectUserRepository = $this->getDoctrine()->getRepository("AppBundle:ProjectUser");
        $projectUser = $projectUserRepository->findOneBy(array("project" => $share->getProject(),
                                                                    "user" => $this->getUser()));
        if ( null != $projectUser){
            return new JsonResponse(array('error' => 'already member'), $status = 400);
        }

        $projectUser = new ProjectUser();
        $projectUser->setProject($share->getProject());
        $projectUser->setUser($this->getUser());
        $projectUser->setProjectRole($share->getRole());
        $projectUser->setOwner($share->getOwner());

        $em = $this->getDoctrine()->getManager();
        $em->persist($projectUser);
        $em->flush();

        $em->remove($share);
        $em->flush();

        return $this->redirectToRoute('dashboard');
    }

    private function clean($string) {
        $string = str_replace(' ', '-', $string);

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }
}
