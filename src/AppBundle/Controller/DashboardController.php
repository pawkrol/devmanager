<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectUser;
use AppBundle\Utils\ProjectType;
use DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{

    private $projectId;

    /**
     * @Route("/dashboard")
     */
    public function indexAction(Request $request){
        $this->projectId = $request->query->get("projectId");
        $selectedProject = null;
        if ($this->projectId === null){
            $this->projectId = -1;
        } else {
            $selectedProject = $this->getUserProject();
        }

        $user = $this->getUser();
        $logout_path = $this->generateUrl('logout');
        $form = $this->handleForm($request);

        $projectUserRepository = $this->getDoctrine()->getRepository('AppBundle:ProjectUser');
        $projects = $projectUserRepository->getUserProjects($user);

        return $this->render('dashboard.html.twig', array( 'user' => $user, 'logout_path' => $logout_path,
                                                'form' => $form->createView(),
                                                'projects' => $projects,
                                                'projectId' => $this->projectId,
                                                'selectedProject' => $selectedProject));
    }

    private function handleForm(Request $request){
        $project = new Project();
        $form = $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $project = $form->getData();

            $currentDate = new DateTime(date("Y-m-d H:i:s"));

            $project->setDateCreate($currentDate);

            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            $projectUser = new ProjectUser();
            $projectUser->setUser($this->getUser());
            $projectUser->setProject($project);
            $projectUser->setProjectRole('Project Manager');
            $projectUser->setOwner(true);

            $em->persist($projectUser);
            $em->flush();

            $this->projectId = $project->getId();

            unset($project);
            unset($form);
            $project = new Project();
            $form = $this->createForm(ProjectType::class, $project);
        }

        return $form;
    }

    private function getUserProject(){
        $em = $this->getDoctrine()->getRepository('AppBundle:ProjectUser');
        $isOwner = $em->isUserOwner($this->getUser(), $this->projectId);

        if (!$isOwner) {
            $this->projectId = -1;
            return null;
        }

        $em = $this->getDoctrine()->getRepository('AppBundle:Project');
        return $em->findOneBy(["id" => $this->projectId]);
    }

}
