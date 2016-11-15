<?php
/**
 * Created by PhpStorm.
 * User: pawkrol
 * Date: 11/5/16
 * Time: 7:06 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Utils\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RegisterController extends Controller
{
    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request){
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $user->setRole('ROLE_USER');

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('security/register.html.twig', array('form' => $form->createView()));
    }
}