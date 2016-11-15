<?php
/**
 * Created by PhpStorm.
 * User: pawkrol
 * Date: 10/23/16
 * Time: 5:06 PM
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LoginController extends Controller
{

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request){
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirect($this->generateUrl('homepage'));
        }

        $authutils = $this->get('security.authentication_utils');
        $error = $authutils->getLastAuthenticationError();
        $lastUsername = $authutils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error
        ));
    }
}