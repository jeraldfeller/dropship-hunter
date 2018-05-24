<?php
/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 5/24/2018
 * Time: 12:49 PM
 */

namespace AppBundle\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use AppBundle\Entity\Users;

class UsersController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginPageAction()
    {
            // replace this example code with whatever you need
            return $this->render('user/login.html.twig');
    }

    /**
     * @Route("/admin")
     */
    public function adminAction(){
        $isLoggedIn = $this->get('session')->get('isLoggedIn');
        if ($isLoggedIn) {
            $users = $this->getDoctrine()
                ->getRepository('AppBundle:Users')
                ->find(1);

            return $this->render('admin/admin.html.twig', [
                'user' => array('username' => $users->getUserName(), 'password' => $users->getPassword())
            ]);
        }else{
            return $this->redirect('/login');
        }
    }
    /**
     * @Route("/login-action")
     */
    public function loginAction(){
        $email = $_POST['_username'];
        $password = $_POST['_password'];
        $users = $this->getDoctrine()
            ->getRepository('AppBundle:Users')
            ->findOneBy(
                array('userName' => $email, 'password' => $password)
            );

        if(count($users) == 1){
            $return = array('success' => true,
                'id' => $users->getId(),
                'username' => $users->getUserName(),
                'password' => $users->getPassword()
            );

            $this->get('session')->set('isLoggedIn', true);
            $this->get('session')->set('userData', $return);


            //return $this->redirectToRoute('homepage', array(), 301);
            return $this->redirect('/');


        }else{
            return $this->redirect('login?message=Invalid Username or Password');
        }


    }


    /**
     * @Route("/update-user ")
     */
    public function userUpdateAction(){
        $em = $this->getDoctrine()->getManager();
        $data = $_POST;

        $entity = $em->getRepository('AppBundle:Users')->find(1);
        $entity->setUserName($data['username']);
        $entity->setPassword($data['password']);
        $em->flush();

        return new Response(json_encode(true));
    }

    /**
     * @Route("/logout")
     */
    public function logout(){
        $this->get('session')->clear();
        return $this->redirectToRoute('homepage', array(), 301);
    }


}