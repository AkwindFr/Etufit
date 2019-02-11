<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route; // Include Route component
use Symfony\Component\HttpFoundation\Request; // Include Request component
use Symfony\Component\HttpFoundation\Response; // Include Response component
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; // Include NotFoundHttpException component
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException; // Include AccessDeniedHttpException component
use Symfony\Component\HttpKernel\Exception\HttpException; // Include HttpException component
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Include Abstract Controller Bundle
use Symfony\Bundle\FrameworkBundle\Controller\Controller;  // Inclusion de la class SF controller

class MainController extends AbstractController
{

    /**
     * @Route("/", name="index")
     */
    public function index(){ // Function to return the view "index"
    // Cannot access if you are loged
        if($this->get('session')->has('account')){
            return $this->render('user-dashboard.html.twig');
        }
            return $this->render('user-index.html.twig');
    }

    /**
     * @Route("/register/", name="register")
     */
    public function register(){ // Function to return the view "register"
    // Cannot access if you are loged
        if($this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }

        return $this->render('user-register.html.twig');
    }

    /**
     * @Route ("/legalmention/", name="legalmention")
     */
    public function legalmention(){
        return $this->render('user-legalmention.html.twig');
    }

    /**
     * @Route ("/whois/", name="whois")
     */
    public function whois(){
        return $this->render('user-whois.html.twig');
    }
}