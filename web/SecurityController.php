<?php

namespace Ffjv\FoBundle\Controller;

use Ffjv\BoBundle\Entity\User;
use Ffjv\BoBundle\Form\CreateUserType;
use Ffjv\FoBundle\Form\Type\User\SendMailType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction()
    {

        // if user already registered
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->addFlash('success', 'vous êtes déjà connecté !');
            return $this->redirectToRoute('ffjv_fo_home_index');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('@FfjvFo/Security/login.html.twig', array(
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(){
        $user = new User();
        $registerForm = $this->getRegisterForm($user);

        return $this->render('@FfjvFo/Security/register.html.twig', array(
            'form' => $registerForm->createView()
        ));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createUserAction(Request $request){
        $user = new User();
        $registerForm = $this->getRegisterForm($user);
        $registerForm->handleRequest($request);
        $data = $registerForm->getData();


        $em = $this->getDoctrine()->getManager();
        // if email exist don't record and send response
        if($em->getRepository('FfjvBoBundle:User')->findOneBy(array('email' => $data->getEmail()))){
            $this->addFlash('error', 'Cette email exist Déjà !!');
            $registerForm->get('email')->addError(new FormError('Cette email exist déjà !!'));
            return $this->render('@FfjvFo/Security/register.html.twig', array(
                'form' => $registerForm->createView()
            ));
        }
        // if username exist don't record and send response
        if($em->getRepository('FfjvBoBundle:User')->findOneBy(array('username' => $data->getUsername()))){
            $this->addFlash('error', 'Ce username exist Déjà !!');
            $registerForm->get('username')->addError(new FormError('Ce username exist Déjà !!'));
            return $this->render('@FfjvFo/Security/register.html.twig', array(
                'form' => $registerForm->createView()
            ));
        }

        if($registerForm->isValid()){
            $em = $this->getDoctrine()->getManager();
            //set password
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            //set zipcode
            $user->setIdZipCode($this->getIdZipCode($user->getZipCode(), $user->getCountry()));
            //persiste and flush
            $em->persist($user);
            $em->flush();

            //SendMail to confirm email
            $this->sendMailToConfirmation($user);

            $this->addFlash('success', 'Un email vous à êtait envoyer');
            return $this->redirect($this->generateUrl('ffjv_fo_security_confirmation', array('userId' => $user->getId())));
        }
        return $this->render('@FfjvFo/Security/register.html.twig', array(
            'form' => $registerForm->createView()
        ));
    }

    /**
     * @param int $userId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function getConfirmationRegisteringAction($userId){
        // if user already registered
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') && $this->getUser()->getStatus() == 1) {
            $this->addFlash('success', 'vous êtes déjà connecté !');
            return $this->redirectToRoute('ffjv_fo_home_index');
        }
        $this->addFlash('success', 'vous ête maintenant enregistrer ! Veuillez confirmer votre adresse email .');
        $form = $this->getSendMailForm($userId);
        return $this->render('@FfjvFo/Security/confirmation.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function sendMailToConfirmationAction(Request $request){

        $form = $this->getSendMailForm('');
        $form->handleRequest($request);
        $userId = $form->getData()['userId'];
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('FfjvBoBundle:User')->findOneBy(array('id' => $form->getData()['userId']));
            if($user->getStatus() == 0){
                $user->setActivationCode(base_convert(md5(uniqid(mt_rand(), true)), 16, 36));
                $em->persist($user);
                $em->flush();
                //SendMail to confirm email
                $this->sendMailToConfirmation($user);

                $this->addFlash('success', 'Un email vous à êtait envoyer');
            }
        }

        return $this->redirect($this->generateUrl('ffjv_fo_security_confirmation', array('userId' => $userId)));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function checkstatusAction(){
        if($this->getUser()->getStatus()){
            $this->addFlash('success', 'Bienvenue sur l\'intranet FFJV');
            return $this->redirectToRoute("ffjv_fo_home_index");
        }
        $userId = $this->getUser()->getId();
        $form = $this->getSendMailForm($userId);
        return $this->render('@FfjvFo/Security/confirmation.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param int $userId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmEmailFromUserAction($userId){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('FfjvBoBundle:User')->findOneBy(array('id' => $userId));

        if($user && $user->getStatus() == 0){
            $user->setRoles(array('ROLE_USER_CONFIRMED'));
            $user->setStatus(1);
            $user->setActivationCode(base_convert(md5(uniqid(mt_rand(), true)), 16, 36));
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Félicitation votre compte est activé ! Il ne vous reste plus qu\'a vous connecter !');
            return $this->redirectToRoute("login");
        }
        return $this->redirectToRoute("ffjv_fo_home_index");
    }

    /* ****** PRIVATE ***************************************** */
    /**
     * @param int $userId
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getSendMailForm($userId){
        $form = $this->createForm(new SendMailType($userId), null, array(
            'action' => $this->generateUrl('ffjv_fo_security_send_mail_confirmation'),
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * @param User $user
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getRegisterForm(User $user){
        $registerForm = $this->createForm(new CreateUserType(true), $user, array(
            'action' => $this->generateUrl('ffjv_fo_security_createuser'),
            'method' => 'POST'
        ));
        $registerForm->add('submit', 'submit', array(
            'label' => 'enregistrer'
        ));
        return $registerForm;
    }

    /**
     * @param string $zipCode
     * @param string $country
     *
     * @return string
     */
    private function getIdZipCode($zipCode, $country = "FR"){

        $zipCode = strtoupper($zipCode);
        if($country == "FR"){
            if(strlen($zipCode) > 3){
                return substr($zipCode, 0, 2);
            }
            return $zipCode;
        }
        return substr($zipCode, 0, 2);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    private function sendMailToConfirmation(User $user){

        $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom('contact@ffjv.org')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView('FfjvFoBundle:Emails:registration.html.twig',
                    array('name' => $user->getUsername(), 'code' => $user->getActivationCode())
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        return true;
    }
}
