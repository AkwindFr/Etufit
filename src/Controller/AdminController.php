<?php

// Namespace toujours en ciblant le chemin du fichier dans Symfony, en remplaçant "src\" par "App\"
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route; // include Component Annotation
use Symfony\Component\HttpFoundation\Response;  // include Component Response
use Symfony\Component\HttpFoundation\Request;  // include Component Request
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;  // Include Bundle Controller
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException; // include Component AccessDeniedHttpException
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; // include Component NotFoundHttpException
use Symfony\Bundle\FrameworkBundle\Controller\Controller;  // Inclusion de la class SF controller
use App\Entity\User; // include entity User
use App\Entity\Opening; // include entity Opening
use DateTime; // include object Datetime

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route ("/showHistoric/", name="historic")
     */
    public function showHistoric(){ // Function to showHistoric (list of every reservation by ordrer of date)
    // Cannot access if you are not loged as administrator
        if($this->get('session')->get('account')->getStatus() < 3){
            throw new AccessDeniedHttpException();
        }

        $openingsRepo = $this->getDoctrine()->getRepository(Opening::class); // Connecting to opening repository

        $openings = $openingsRepo->findBy(array(), array('open' => 'DESC')); // Get every openings

        foreach ($openings as $opening){ // Get every users by openings for total
            foreach ($opening->getUser() as $user) {
                $users[] = array(
                    'id' => $user->getId()
                );
            }
        }

        if(isset($users)){
            $total = count($users); // Count the total of reservations
                return $this->render('admin-historic.html.twig', array('openings' => $openings, 'total'=> $total)
            );
        } else {
            $total = '0'; // if empty set total to 0
                return $this->render('admin-historic.html.twig', array('openings' => $openings, 'total'=> $total)
            );
        }

        return $this->render('admin-historic.html.twig', array("openings" => $openings)); // Return view "historic" with openings (array)
    }

    /**
     * @Route ("/users/", name="users")
     */
    public function users(){ // Function to showUsers (list of every user by ordrer of letter)
    // Cannot access if you are not loged as administrator
        if($this->get('session')->get('account')->getStatus() < 3){
            throw new AccessDeniedHttpException();
        }

        $usersRepo = $this->getDoctrine()->getRepository(User::class); // Connecting to opening repository

        $users = $usersRepo->findBy(array(), array('name' => 'ASC')); // Get every openings

        return $this->render('admin-users.html.twig', array("users" => $users)); // Return view "users" with users (array)
    }
}