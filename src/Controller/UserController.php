<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route; // Include Route component
use Symfony\Component\HttpFoundation\Request; // Include Request component.
use Symfony\Component\HttpFoundation\Response; // Include Response component.
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; // Include NotFoundHttpException component
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException; // Include AccessDeniedHttpException component
use Symfony\Component\HttpKernel\Exception\HttpException; // Include HttpException component
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Include Controller bundle
use Symfony\Bundle\FrameworkBundle\Controller\Controller;  // Inclusion de la class SF controller
use App\Entity\User; // Include User entity
use App\Entity\Opening; // Include Opening entity
use App\Service\Recaptcha; // Include recaptcha service
use DateTime; // include object Datetime

class UserController extends AbstractController
{
    /**
     * @Route("/activation/{id}/{token}/", name="activation", requirements={"id"="[1-9][0-9]*", "token"="[a-zA-Z0-9]{32}"})
     */
    public function activation($id, $token){ // Function to activate an account
    // Cannot access if account NOT activated
        if($this->get('session')->get('account') && $this->get('session')->get('account')->getActive() == 1){
            throw new AccessDeniedHttpException('Votre compte est dékà actif');
        }

        $userRepo = $this->getDoctrine()->getRepository(User::class); // Get User repository
        $user = $userRepo->findOneById($id); // Check if the User is founded in database

        if(!is_object($user)){
        // if the method findOneById() returns a NON object
            throw new NotFoundHttpException('Lien Invalide'); // There is no reason to specify why the link is invalid
        }

        if($user->getToken() != $token){
        // If the token in db don't match with the token in URL
            throw new NotFoundHttpException('Lien Invalide'); // There is no reason to specify why the link is invalid
        }

        $em = $this->getDoctrine()->getManager();
        $user->setActive(1); // Account state pass from 0 (NOT activated) to 1 (Activated)
        $em->flush();

        $success = ', votre compte a été activé avec succès !';

        return $this->render('user-activation.html.twig', array('user' => $user, 'success' => $success)); //Return the view with array
    }

    /**
     * @route ("/logout/", name="logout")
     */
    public function logout(){ // Function to logout
    // Cannot access if you are not loged
        if(!$this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }

        $this->get('session')->remove('account'); // Remove variable account

        return $this->render('user-logout.html.twig');
    }

    /**
     * @Route ("/dashboard/", name="dashboard")
     */
    public function dashboard(){
        // Cannot access if you are not loged
            if(!$this->get('session')->has('account')){
                throw new AccessDeniedHttpException();
            }

        // Cannot access if account NOT activated
            if($this->get('session')->get('account')->getActive() == 0){
                throw new AccessDeniedHttpException();
            }

            return $this->render('user-dashboard.html.twig');
        }

    /**
     * @Route("/passwordForget/", name="passwordForget")
     */
    public function passwordForget(){ // Function to show the forget password page
        // Cannot access if you are loged
            if($this->get('session')->has('account')){
                throw new AccessDeniedHttpException();
            }

            return $this->render('reset-email.html.twig');
    }

    /**
     * @Route("/resetPassword/{id}/{token}/", name="resetPassword", requirements={"id"="[1-9][0-9]*", "token"="[a-zA-Z0-9]{32}"})
     */
    public function resetPassword($id, $token){ // Function to reset password
    // Cannot access if you are loged
        if($this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }
    // Using repository : User
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepo->findOneById($id);

        if(!is_object($user)){ // if the method findOneById() returns a NON object
            throw new NotFoundHttpException('Lien invalide');
        }
        if($user->getToken() != $token){ // If the token in db don't match with the token in URL
            throw new NotFoundHttpException('Lien invalide');
        }

        return $this->render('reset-password.html.twig', array('id' => $id));// return view passwordChange and array id for verification
    }

    /**
     * @Route ("/changePassword/", name="changePassword")
     */
    public function changePassword(){
    // Cannot access if you are not loged
        if(!$this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }

    // Cannot access if account NOT activated
        if($this->get('session')->get('account')->getActive() == 0){
            throw new AccessDeniedHttpException();
        }

        return $this->render('change-password.html.twig');
    }

    /**
     * @Route ("/myReservations/", name="myReservations")
     */
    public function showHistoric(){ // Function to showHistoric (list of every reservation by ordrer of date)
    // Cannot access if you are not loged
        if(!$this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }

        // Cannot access if account NOT activated
        if($this->get('session')->get('account')->getActive() == 0){
            throw new AccessDeniedHttpException();
        }

        $user_id = $this->get('session')->get('account')->getId(); // get to user repository

        $userRepo = $this->getDoctrine()->getRepository(User::class); // Connecting to opening repository
        $user = $userRepo->findOneById($user_id); // Check if user is founded

        foreach ($user->getOpening() as $opening) {
            $openings[] = array(
                'id' => $opening->getId(),
                'referent' => $opening->getReferent(),
                'places' => $opening->getPlaces(),
                'open' => $opening->getOpen(),
                'close' => $opening->getClose()
            );
        }

        if(isset($openings)){
            return $this->render('user-myReservations.html.twig', array('openings' => $openings));
        } else {
            return $this->render('user-myReservations.html.twig');
        }
    }

    /**
     * @Route("/profil/", name="profil")
     */
    public function profil(){ // Function to return the view "profil"
    // Cannot access if you are NOT loged
        if(!$this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }

        // Cannot access if account NOT activated
        if($this->get('session')->get('account')->getActive() == 0){
            throw new AccessDeniedHttpException();
        }

        $account = $this->get('session')->get('account'); // Get the account variable who contains all the informations about the connected user

        return $this->render('user-profil.html.twig', array("account" => $account));
    }

    /**
     * @Route("/calendar/", name="calendar")
     */
    public function calendar(){ // Function to return the view "calendar"
    // Cannot access if you are not loged
        if(!$this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }
    // Cannot access if account NOT activated
        if($this->get('session')->get('account')->getActive() == 0){
            throw new AccessDeniedHttpException();
        }

        return $this->render('user-calendar.html.twig');
    }
}