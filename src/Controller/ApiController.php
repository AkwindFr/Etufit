<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route; // include Component Annotation
use Symfony\Component\HttpFoundation\Response;  // include Component Response
use Symfony\Component\HttpFoundation\Request;  // include Component Request
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;  // include Bundle Controller
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException; // include Component AccessDeniedHttpException
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; // include Component NotFoundHttpException
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;  // Inclusion de la class SF controller
use App\Entity\User; // include entity User
use App\Entity\Opening; // include entity Opening
use DateTime; // include object Datetime
use App\Service\Recaptcha; // include Service recaptcha

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
/** User Account section */

    /**
     * @Route("/registerValidate/", name="registerValidate", methods={"POST"})
     */
    public function register(Request $request, Recaptcha $captcha, \Swift_Mailer $mailer){ // Function to chack and also valid registration

    // Cannot access if you are loged
        if($this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }
    // Get every form fields by get method
        $email = $request->request->get('emailRegister');
        $name = $request->request->get('nameRegister');
        $password = $request->request->get('passwordRegister');
        $confirmPassword = $request->request->get('confirmRegister');
        $studentId = $request->request->get('studentId');

    // Verifications
        if(!preg_match('#^[a-zA-Z -\'áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]{2,60}$#', $name)){
            $errors['name'] = true;
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors['email'] = true;
        }

        if(!preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[\@\#\.\\\{\}\[\]\&\~\/\,])(?=.*\d).{5,250}$#', $password)){
            $errors['password'] = true;
        }

        if($password != $confirmPassword){
            $errors['confirmPassword'] = true;
        }

        if(!preg_match('#^[0-9]{9,10}[a-zA-Z]{1,2}$#', $studentId)){
            $errors['studentId'] = true;
        }

        if(!$captcha->recaptcha_valid($request->request->get('g-recaptcha-response'), $request->server->get('REMOTE_ADDR'))){
            $errors['captcha'] = true;
        }

        if(!isset($errors)){
            $userRepo = $this->getDoctrine()->getRepository(User::class); // Get User repository
            $verif = $userRepo->findOneByEmail($email); // Check if the User is founded

            if(is_object($verif)){ // if the method findOneByEmail() returns an object
                $errors['emailBusy'] = true;
            } else {
                $em = $this->getDoctrine()->getManager();

                $token = md5(time() . rand() . uniqid()); // Generate a random token (one uniq, per account)

                $newUser = new User(); // Create new instance of User
            // Hydrating the object
                $newUser->setEmail($email)
                    ->setName($name)
                    ->setPassword(password_hash($password, PASSWORD_BCRYPT)) // Password hashing
                    ->setStudentId($studentId)
                    ->setStatus(1) // Set status to 1 (simple user) by default
                    ->setToken($token)
                    ->setActive(0) // confirmation mail to activate
                ;
                $em->persist($newUser);
                $em->flush(); // Add it in database

                $message = (new \Swift_Message('Sujet du mail')) // Generating confirmation email
                ->setFrom('etufitteam@gmail.com')
                ->setTo($email)
                ->setBody($this->renderView('emails/register.html.twig', array('newUser' => $newUser)),'text/html')
                ->addPart($this->renderView('emails/register.txt.twig', array('newUser' => $newUser)),'text/plain');

                $mailer->send($message); // Send the email to new user

                return $this->json(array( // return json array to show success message
                    "success" => true,
                    "email" => $email
                ));
            }
        }
        if(isset($errors)){
            return $this->json(array( // return json array to show error message
                "errors" => $errors
            ));
        }
    }

    /**
     * @Route("/loginValidate/", name="loginValidate", methods={"POST"})
     */
    public function loginValidate(Request $request){ // Function to login

    // Cannot access if you are loged
        if($this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }

    // Get every form fields by get method
        $email = $request->request->get('email');
        $password = $request->request->get('password');

    // Verifications
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors['email'] = true;
        }

        if(!preg_match('#^.{5,300}$#', $password)){
            $errors['password'] = true;
        }

        if(!isset($errors)){
            $userRepository = $this->getDoctrine()->getRepository(User::class); // Get User repository
            $userVerif = $userRepository->findOneByEmail($email); // Check if the User is founded

            if(!is_object($userVerif)){ // if the method findOneByEmail() returns a NON object
                $errors['undefined_account'] = true;
            } else {
                if(password_verify($password, $userVerif->getPassword())){
                // Cannot access if account NOT activated
                    if($userVerif->getActive() == 0){
                        $errors['unactivated_account'] = true;
                    } else {
                    // If mail and password match, log process
                        $this->get('session')->set('account', $userVerif);

                        return $this->json(array( // return json array to show success message
                            "success" => true
                        ));
                    }
                } else {
                    $errors['invalid_password'] = true;
                }
            }
        }

        if(isset($errors)){
            return $this->json(array( // return json array to show error message
                "errors" => $errors
            ));
        }
    }

    /**
     * @Route ("/deleteProfil/", name="deleteProfil", methods={"POST"})
     */
    public function profileDeleteUser(Request $request){ // Function to delete a User
    // Cannot access if you are not loged
        if(!$this->get('session')->has('account')){
                throw new AccessDeniedHttpException();
            }

            $id_user = $this->get('session')->get('account')->getId();

            $userRepository = $this->getDoctrine()->getRepository(User::class); // Get User repository
            $user = $userRepository->findOneById($id_user); // Check if the id is founded

            if(empty($user)){
            // If array is empty, there is no user who match
                $errors['user_undifined'] = true;
            } else {
            // Else delete user
                $em = $this->getDoctrine()->getManager();
                $em->remove($user);
                $em->flush();

                $this->get('session')->remove('account');

                $redirect = 'http://localhost:8000';

                return $this->json(array( // return json array to show success message
                    "success_delete_user" => true,
                    "redirect" => $redirect
                ));
            }

            if(isset($errors)){ // return json array to show error message
                return $this->json(array(
                    "errors" => $errors
            ));
            }
        }

/** END of User Account section */

/** Password route section */

    /**
     * @Route("/passwordMail/", name="passwordMail", methods={"POST"})
     */
    public function passwordMail(Request $request, \Swift_Mailer $mailer){ // Function to send mail reset password
    // Cannot access if you are loged
        if($this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }

        $email = $request->request->get('email');

    // Verification
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors['email'] = true;
        }

        if(!isset($errors)){
            $userRepository = $this->getDoctrine()->getRepository(User::class); // Get User repository
            $user = $userRepository->findOneByEmail($email); // Check if the user is founded

            if(!is_object($user)){ // if the method findOneByEmail() returns a NON object
                $errors['undefined_account'] = true;
            } else {

                $message = (new \Swift_Message('Sujet du mail')) // Generating forget Password email
                ->setFrom('etufitteam@gmail.com')
                ->setTo($email)
                ->setBody($this->renderView('emails/password.html.twig', array('user' => $user)),'text/html')
                ->addPart($this->renderView('emails/password.txt.twig', array('user' => $user)),'text/plain');

                $mailer->send($message); // Send the email to user

                return $this->json(array(
                    "success" => true
                ));
            }
        }

        if(isset($errors)){
            return $this->json(array(
                "errors" => $errors
            ));
        }
    }

    /**
     * @Route ("/passwordConfirm/{id}/", name = "passwordConfirm", requirements={"id"="[1-9][0-9]*"})
     */
    public function passwordConfirm(Request $request, $id){ // function to check password
        // get every form fields
        $password = $request->request->get('newPassword');
        $confirmPassword = $request->request->get('confirmPassword');

        // verifications
        if(!preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[\@\#\.\\\{\}\[\]\&\~\/\,])(?=.*\d).{5,250}$#', $password)){
            $errors['password'] = true;
        }

        if($password != $confirmPassword){
            $errors['confirmPassword'] = true;
        }

        if(!isset($errors)){

            $userRepository = $this->getDoctrine()->getRepository(User::class); // Get user repository
            $user = $userRepository->findOneById($id); // Check if the user is founded

            if(!is_object($user)){
                $errors['undefined_user'] = true;
            } else {

            $em = $this->getDoctrine()->getManager();
            $user->setPassword(password_hash($password, PASSWORD_BCRYPT)); // Password hashing
            $em->merge($user);
            $em->flush(); // Add it in db

            return $this->json(array(
                "success" => true
            ));
            }
        }

        if(isset($errors)){
            return $this->json(array(
                "errors" => $errors
            ));
        }
    }

    /**
     * @Route ("/newPassword/", name="newPassword")
     */
    public function newPassword(Request $request){
    // Cannot access if you are not loged
        if(!$this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }

    // get every form fields
        $password = $request->request->get('profilPassword');
        $newPassword = $request->request->get('newPassword');
        $confirmPassword = $request->request->get('confirmPassword');

    // Verifications
        if(!preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[\@\#\.\\\{\}\[\]\&\~\/\,])(?=.*\d).{5,250}$#', $password)){
            $errors['password'] = true;
        }

        if(!preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[\@\#\.\\\{\}\[\]\&\~\/\,])(?=.*\d).{5,250}$#', $newPassword)){
            $errors['newPassword'] = true;
        }

        if($newPassword != $confirmPassword){
            $errors['confirmPassword'] = true;
        }

        if(!isset($errors)){
            $user_id = $this->get('session')->get('account')->getId();
            $userRepo = $this->getDoctrine()->getRepository(User::class); // Get User repository
            $user = $userRepo->findOneById($user_id); // Check if the User is founded

            if(!is_object($user)){
                $errors['user_undefined'] = true;
            } else {

                if(!password_verify($password, $user->getPassword())){
                    $errors['wrong_password'] = true;
                } else {

                    $em = $this->getDoctrine()->getManager();
                    $user->setPassword(password_hash($newPassword, PASSWORD_BCRYPT));
                    $em->merge($user);
                    $em->flush(); // Add it in database

                    return $this->json(array(
                        "success" => true
                    ));
                }
            }
        }

        if(isset($errors)){
            return $this->json(array(
                "errors" => $errors
            ));
        }
    }

/** END of Password route section */

/** Calendar section */

    /**
     * @Route("/openings/", name="apiGetOpenings")
     */
    public function apiGetOpenings(Request $request){ // Function to fill calendar page

    // Cannot access if you are not loged
        if(!$this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }
    // Cannot access if account NOT activated
        if($this->get('session')->get('account')->getActive() == 0){
            throw new AccessDeniedHttpException();
        }

        $openingsRepo = $this->getDoctrine()->getRepository(Opening::class); // Get Opening repository
        $openings = $openingsRepo->findAll(); // Collecting all the reservations

    // Put some options found in fullcalendar doc
        foreach($openings as $opening){
            $data[] = array(
                "id" => $opening->getId(),
                "referent" => $opening->getReferent(),
                "places" => $opening->getPlaces(),
                "start" => $opening->getOpen()->format('Y-m-d\TH:i:s'),
                "end" => $opening->getClose()->format('Y-m-d\TH:i:s')
            );
        }
        if(isset($data)){
            return $this->json($data); // Return every openings in json array
        } else {
            return $this->render('calendar.html.twig');
        }
    }

    /**
     * @Route("/addSlot/", name="addSlot", methods={"POST"})
     */
    public function addSlot(Request $request){ // Function to create new Openings

    // Cannot access if you are not loged as administrator
        if($this->get('session')->get('account')->getStatus() < 3){
            throw new AccessDeniedHttpException();
        }

    // Get every form fields by get method
        $referent = $request->request->get('referent');
        $places = $request->request->get('places');
        $open = $request->request->get('open');
        $close = $request->request->get('close');

    // Verifications
        if(!preg_match('#^[a-zA-Z -\'áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]{2,60}$#', $referent)){
            $errors['referent'] = true;
        }

        if(!preg_match('#^(\d){1,2}$#', $places)){
            $errors['places'] = true;
        }

        if(!preg_match('#^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})$#', $open)){
            $errors['open'] = true;
        }

        if(!preg_match('#^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})$#', $close)){
            $errors['close'] = true;
        }

        if(!isset($errors)){
            $currentTime = getdate()[0];
        // if date close is anterior to date open
            $dateStart = new Datetime($open);
            $timestampStart = $dateStart->getTimestamp();
            $dateEnd = new Datetime($close);
            $timestampEnd = $dateEnd->getTimestamp();

            if($currentTime > $timestampStart){
                $errors['dateOver'] = true;
            } elseif($timestampStart > $timestampEnd) {
                $errors['dateEnd'] = true;
            } else {
            // If no errors create new entity in Openings
                $newOpening = new Opening();
                $newOpening->setReferent($referent);
                $newOpening->setPlaces($places);
                $newOpening->setOpen(new DateTime($open));
                $newOpening->setClose(new DateTime($close));

            // And merge in datatbase
                $em = $this->getDoctrine()->getManager();
                $em->merge($newOpening);
                $em->flush();

                return $this->json(array( // return json array to show success message
                    "success" => true
                ));
                }
            }

        if(isset($errors)){
            return $this->json(array( // return json array to show error message
                "errors" => $errors
            ));
        }
    }

    /**
     * @Route ("/reserveSlot/", name="reserve", methods={"POST"})
     */
    public function reserve(Request $request){ // Function to reserve a slot
    // Cannot access if you are not loged
        if(!$this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }

    // Cannot access if account NOT activated
        if($this->get('session')->get('account')->getActive() == 0){
            throw new AccessDeniedHttpException();
        }

        $id_user = $this->get('session')->get('account')->getId(); //Get the id of current user
        $id_opening = $request->request->get('id_opening'); // Get form field by get method

        $openingRepository = $this->getDoctrine()->getRepository(Opening::class); // Get Opening repository
        $opening = $openingRepository->findOneById($id_opening); // Check if opening is founded

        $userRepository = $this->getDoctrine()->getRepository(User::class); // Get User repository
        $user = $userRepository->findOneById($id_user); // Check if user is founded

    // Verifications
        if(!is_numeric($id_opening) || $id_opening < 0){
            $errors['id'] = true;
        }

        if(!is_object($opening)){ // if the method findOneById($opening) returns a NON object
            $errors['id_notFound'] = true;
        }

        if(!is_object($user)){ // if the method findOneById($user) returns a NON object
            $errors['user_notFound'] = true;
        }

        if($opening->getUser()->contains($user)){ // if a link between the opening and the user already exist
            $errors['exist'] = true;
        }

        if(!isset($errors)){
            $close = $opening->getClose();
            $currentTime = getdate()[0];
            $timestampEnd = $close->getTimestamp();
            if($currentTime > $timestampEnd){
                $errors['dateOver'] = 'Vous ne pouvez pas agir sur un événement dépassé';
            } else {
        // Create the relation between entity User and Opening
            $user->addOpening($opening);
            $opening->addUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json(array( // return json array to show success message
                "success" => true
            ));
            }
        }

        if(isset($errors)){
            return $this->json(array( // return json array to show error message
                "errors" => $errors
            ));
        }
    }

    /**
     * @Route ("/cancelSlot/", name="cancel", methods={"POST"})
     */
    public function cancel(Request $request){ // Function to unreserve a reservation
    // Cannot access if you are not loged
        if(!$this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }

        $id_user = $this->get('session')->get('account')->getId(); //Get the id of current user
        $id_opening = $request->request->get('id_opening'); // Get form field by get method

        $openingRepository = $this->getDoctrine()->getRepository(Opening::class); // Get Opening repository
        $opening = $openingRepository->findOneById($id_opening); // Check if the opening is founded

        $userRepository = $this->getDoctrine()->getRepository(User::class); // Get User repository
        $user = $userRepository->findOneById($id_user); // Check if the user is founded

        // Verifications
        if(!is_numeric($id_opening) || $id_opening < 0){
            $errors['id'] = true;
        }

        if(!is_object($opening)){ // if the method findOneById($opening) returns a NON object
            $errors['id_notFound'] = true;
        }

        if(!is_object($user)){ // if the method findOneById($user) returns a NON object
            $errors['user_notFound'] = true;
        }

        if(!$opening->getUser()->contains($user)){ // if a link between the opening and the user already exist
            $errors['exist'] = true;
        }

        if(!isset($errors)){
            $close = $opening->getClose();
            $currentTime = getdate()[0];
            $timestampEnd = $close->getTimestamp();

            if($currentTime > $timestampEnd){
                $errors['dateOver'] = 'Vous ne pouvez pas agir sur un événement dépassé';
            } else {
        // Delate the relation between entity User and Opening
            $user->removeOpening($opening);
            $opening->removeUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json(array( // return json array to show success message
                "success" => true
            ));
            }
        }

        if(isset($errors)){
            return $this->json(array( // return json array to show error message
                "errors" => $errors
            ));
        }
    }

    /**
     * @Route ("/deleteSlot/", name="deleteSlot", methods={"POST"})
     */
    public function deleteSlot(Request $request){ // Function to delete a slot
    // Cannot access if you are not loged as administrator
        if($this->get('session')->get('account')->getStatus() < 3){
            throw new AccessDeniedHttpException();
        }

    // Get form field by get method
        $id_opening = $request->request->get('id_opening');

        // Verifications
        if(!is_numeric($id_opening) || $id_opening < 0){
            $errors['id'] = true;
        }

        if(!isset($errors)){
        // If no errors call manager and custom method findOneByDates()
            $openingRepository = $this->getDoctrine()->getRepository(Opening::class); // Get Opening repository
            $opening = $openingRepository->findOneById($id_opening); // Check if the opening is founded

            if(empty($opening)){
            // If array is empty, there is no opening who match
                $errors['slot_undifinied'] = true;
            } else {
            // Else suppress the opening of database
                $em = $this->getDoctrine()->getManager();
                $em->remove($opening);
                $em->flush();

                return $this->json(array( // return json array to show success message
                    "success" => true
                ));
            }
        }
        if(isset($errors)){
            return $this->json(array( // return json array to show error message
                "errors" => $errors
        ));
        }
    }

    /**
     * @Route ("/deleteOpening/", name="deleteOpening", methods={"POST"})
     */
    public function deleteOpening(Request $request){ // Function to delete a User
    // Cannot access if you are not loged as administrator
        if($this->get('session')->get('account')->getStatus() < 3){
            throw new AccessDeniedHttpException();
        }

    // Get form field by get method
        $id_opening = $request->request->get('id_opening');

    // Verifications
        if(!is_numeric($id_opening) || $id_opening < 0){
        $errors['id'] = true;
        }

        if(!isset($errors)){

            $openingRepository = $this->getDoctrine()->getRepository(Opening::class); // Get Opening repository
            $opening = $openingRepository->findOneById($id_opening); // Check if the opening is founded

            if(empty($opening)){
            // If array is empty, there is no user who match
                $errors['opening_undifinied'] = true;
            } else {
            // Else delete opening
                $em = $this->getDoctrine()->getManager();
                $em->remove($opening);
                $em->flush();

                return $this->json(array( // return json array to show success message
                    "success_delete_opening" => true
                ));
            }
        }

        if(isset($errors)){ // return json array to show error message
            return $this->json(array(
                "errors" => $errors
        ));
        }
    }

/** END of Calendar section */

/** User functions */

    /**
     * @Route("contactUs/", name="contactUs", methods={"POST"})
     */
    public function contactUs(Request $request, \Swift_Mailer $mailer, Recaptcha $captcha){

        // Get every form fields by get method

        $email = $request->request->get('email');
        $msg = $request->request->get('message');

        // Fields verification

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors['email'] = true;
        }

        if(!preg_match('#^[a-zA-Z -\'áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ\ \#\!\^\$\(\)\{\}\[\]\?\"\'\+\.\-]{2,2000}$#', $msg)){
            $errors['msg'] = true;
        }

        if(!$captcha->recaptcha_valid($request->request->get('g-recaptcha-response'), $request->server->get('REMOTE_ADDR'))){
            $errors['captcha'] = true;
        }

        if(!isset($errors)){
            $mail = (new \Swift_Message('Sujet'))
                ->setFrom($email)
                ->setTo('etufitteam@gmail.com')
                ->setBody(
                    $this->renderView('emails/contact.html.twig', array('email' => $email, 'msg' => $msg)), 'text/html'
                )
                ->addPart(
                    $this->renderView('emails/contact.txt.twig', array('email' => $email, 'msg' => $msg)), 'text/plain'
                )
            ;
            $mailer->send($mail);
        }
        if(isset($errors)){
            return $this->json(array('success' => false, 'errors' => $errors));
        }
        return $this->json(array('success' => true));
    }

    /**
     * @Route("/showReservations/", name="reservations", methods={"POST"})
     */
    public function showReservations(Request $request){ // Function to show all users who reserved a slot

    // Cannot access if you are not loged as administrator OR referent
        if($this->get('session')->get('account')->getStatus() < 2){
            throw new AccessDeniedHttpException();
        }

    // Get form field by get method
        $id_opening = $request->request->get('id_opening');

        $openingRepository = $this->getDoctrine()->getRepository(Opening::class); // Get Opening repository
        $opening = $openingRepository->findOneById($id_opening); // Check if the opening is founded

        foreach ($opening->getUser() as $user) {
            $users[] = array(
                'name' => $user->getName()
            );
        }

        if(isset($users)){
            return $this->json(array( // return json array with the total of reservation in array reservations
                "users" => $users
            ));
        } else {
            return $this->json(array( // return json array with the total of reservation in array reservations
                "empty" => true
            ));
        }
    }

    /**
     * @Route("/showTotal/", name="total", methods={"POST"})
     */
    public function showTotal(Request $request){ // Function to show all users who reserved a slot

    // Cannot access if you are not loged as administrator OR referent
        if(!$this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }

    // Get form field by get method
        $id_opening = $request->request->get('id_opening');

        $openingRepository = $this->getDoctrine()->getRepository(Opening::class); // Get Opening repository
        $opening = $openingRepository->findOneById($id_opening); // Check if the opening is founded

        foreach ($opening->getUser() as $user) {
            $users[] = array(
                'id' => $user->getId()
            );
        }

        if(isset($users)){
            $total = count($users); // Count the total of reservations
            return $this->json(array( // return json array with the total of users in array
                "total" => $total,
                'users' => $users
            ));
        } else {
            $total = '0'; // if empty set total to 0
            return $this->json(array( // return json array with the total
                "total" => $total,
                'empty' => true
            ));
        }
    }

/** END of User functions */

/** Admin sections */

    /**
     * @Route ("/statusReferent/", name="statusReferent", methods={"POST"})
     */
    public function statusReferent(Request $request){ // Function to attribute or remove referent Status
    // Cannot access if you are not loged as administrator
        if($this->get('session')->get('account')->getStatus() < 3){
            throw new AccessDeniedHttpException();
        }

    // Get form fields by get method
        $id_user = $request->request->get('id_user');
        $condition = $request->request->get('condition');

    // Verifications
        if(!is_numeric($id_user) || $id_user < 0){
        $errors['id'] = true;
        }

        if(!isset($errors)){

            if($condition == 'true'){ // If the switch input is switched on

                $userRepository = $this->getDoctrine()->getRepository(User::class); // Get Opening repository
                $user = $userRepository->findOneById($id_user); // Check if user is founded

                if(empty($user)){
                // If array is empty, there is no user who match
                    $errors['user_undifinied'] = true;
                } else {
                // Else set the user status to 2 (referent) in database
                    $em = $this->getDoctrine()->getManager();
                    $user->setStatus(2);
                    $em->flush();

                    return $this->json(array( // return json array to show success message
                        "success_attribute_referent" => true
                    ));
                }
            }

            if($condition == 'false'){ // If the switch input is switched off

                $userRepository = $this->getDoctrine()->getRepository(User::class); // Get Opening repository
                $user = $userRepository->findOneById($id_user); // Check if user is founded

                if(empty($user)){
                // If array is empty, there is no user who match
                    $errors['user_undifinied'] = true;
                } else {
                // Else set the user status to 1 (user) in database
                    $em = $this->getDoctrine()->getManager();
                    $user->setStatus(1);
                    $em->flush();

                    return $this->json(array( // return json array to show success message
                        "success_cancel_referent" => true
                    ));
                }
            }
        }

        if(isset($errors)){ // return json array to show error message
            return $this->json(array(
                "errors" => $errors
        ));
        }
    }

    /**
     * @Route ("/statusAdmin/", name="statusAdmin", methods={"POST"})
     */
    public function statusAdmin(Request $request){ // Function to attribute or remove admin Status
        // Cannot access if you are not loged as super administrator
            if($this->get('session')->get('account')->getStatus() < 4){
                throw new AccessDeniedHttpException();
            }

        // Get form fields by get method
            $id_user = $request->request->get('id_user');
            $condition = $request->request->get('condition');

        // Verifications
            if(!is_numeric($id_user) || $id_user < 0){
            $errors['id'] = true;
            }

            if(!isset($errors)){

                if($condition == 'true'){ // If the switch input is switched on

                    $userRepository = $this->getDoctrine()->getRepository(User::class); // Get Opening repository
                    $user = $userRepository->findOneById($id_user); // Check if the opening is founded

                    if(empty($user)){
                    // If array is empty, there is no user who match
                        $errors['user_undifinied'] = true;
                    } else {
                    // Else set the user status to 3 (admin) in database
                        $em = $this->getDoctrine()->getManager();
                        $user->setStatus(3);
                        $em->flush();

                        return $this->json(array( // return json array to show success message
                            "success_attribute_admin" => true
                        ));
                    }
                }

                if($condition == 'false'){ // If the switch input is switched on

                    $userRepository = $this->getDoctrine()->getRepository(User::class); // Get Opening repository
                    $user = $userRepository->findOneById($id_user); // Check if the opening is founded

                    if(empty($user)){
                    // If array is empty, there is no user who match
                        $errors['user_undifinied'] = true;
                    } else {
                    // Else set the user status to 1 (user) in database
                        $em = $this->getDoctrine()->getManager();
                        $user->setStatus(1);
                        $em->flush();

                        return $this->json(array( // return json array to show success message
                            "success_cancel_admin" => true
                        ));
                    }
                }
            }

            if(isset($errors)){ // return json array to show error message
                return $this->json(array(
                    "errors" => $errors
            ));
            }
        }

    /**
     * @Route ("/deleteUser/", name="deleteUser", methods={"POST"})
     */
    public function deleteUser(Request $request){ // Function to delete a User
        // Cannot access if you are not loged as administrator
            if($this->get('session')->get('account')->getStatus() < 3){
                throw new AccessDeniedHttpException();
            }

        // Get form field by get method
            $id_user = $request->request->get('id_user');

        // Verifications
            if(!is_numeric($id_user) || $id_user < 0){
            $errors['id'] = true;
            }

            if(!isset($errors)){

                $userRepository = $this->getDoctrine()->getRepository(User::class); // Get Opening repository
                $user = $userRepository->findOneById($id_user); // Check if the opening is founded

                if(empty($user)){
                // If array is empty, there is no user who match
                    $errors['user_undifinied'] = true;
                } else {
                // Else delete user
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($user);
                    $em->flush();
                    return $this->json(array( // return json array to show success message
                        "success_delete_user" => true
                    ));
                }
            }

            if(isset($errors)){ // return json array to show error message
                return $this->json(array(
                    "errors" => $errors
            ));
            }
        }

/** END of Admin sections */
    }



