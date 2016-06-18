<?php

namespace FfjvFoBundle\Controller;

use FfjvBoBundle\Entity\User;
use FfjvBoBundle\Form\CreateUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use FfjvFoBundle\Form\Type\User\SendMailType;

class SecurityController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction()
    {
        // if user already registered
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->addFlash('success', $this->get('translator')->trans("fo.global.noty.all_ready_connected"));
            return $this->redirectToRoute('ffjv_fo_home_index');
        }

        $form = $this->getLostPasswordForm();

        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('@FfjvFo/Security/login.html.twig', array(
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
            'emailForm'     => $form->createView()
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction()
    {
        // if user already registered
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->addFlash('success', $this->get('translator')->trans("fo.global.noty.all_ready_connected"));
            return $this->redirectToRoute('ffjv_fo_home_index');
        }

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

        if($registerForm->isValid()){
            $em = $this->getDoctrine()->getManager();
            //set password
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            //set zipcode
            $user->setIdZipCode($this->get('user')->getIdZipCode($user->getZipCode(), $user->getNationality()));
            //persiste and flush
            $em->persist($user);
            $em->flush();

            //SendMail to confirm email
            $this->sendMailToConfirmation($user);

            $this->addFlash('success', $this->get('translator')->trans("fo.global.noty.email_sended"));
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
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') && $this->getUser()->getStatus() == 1 && $this->get('security.authorization_checker')->isGranted('ROLE_USER_CONFIRMED')) {
            $this->addFlash('success', $this->get('translator')->trans("fo.global.noty.all_ready_connected"));
            return $this->redirectToRoute('ffjv_fo_home_index');
        }
        $this->addFlash('success', $this->get('translator')->trans("fo.global.noty.email_need_confirmation"));
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

                $this->addFlash('success', $this->get('translator')->trans("fo.global.noty.email_sended"));
            }
        }

        return $this->redirect($this->generateUrl('ffjv_fo_security_confirmation', array('userId' => $userId)));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function checkstatusAction(){
        if($this->getUser()->getStatus()){
            $this->addFlash('success', $this->get('translator')->trans("fo.global.noty.welcome"));
            return $this->redirectToRoute("ffjv_fo_home_index");
        }
        $userId = $this->getUser()->getId();
        $form = $this->getSendMailForm($userId);
        return $this->render('@FfjvFo/Security/confirmation.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param $activationCode
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmEmailFromUserAction($activationCode){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('FfjvBoBundle:User')->findOneBy(array('activationCode' => $activationCode));

        if($user && $user->getStatus() == 0){
            $user->setRoles(array('ROLE_USER_CONFIRMED'));
            $user->setStatus(1);
            $user->setActivationCode(base_convert(md5(uniqid(mt_rand(), true)), 16, 36));
            // create unique licence
            $licence = $this->get('licences_users')->getNewLicences($user);
            $user->setLicence($licence);
            // persist entity and flush it
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', $this->get('translator')->trans("fo.global.noty.acount_successfully"));
            return $this->redirectToRoute("login");
        }
        return $this->redirectToRoute("ffjv_fo_home_index");
    }

    /**
     * @param string    $email
     * @param string    $activationCode
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerNewPasswordAction($email, $activationCode){

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('FfjvBoBundle:User')->findBy(array('email' => $email, 'activationCode' => $activationCode));

        if(!$user){
            return $this->redirectToRoute('ffjv_fo_home_index');
        }
        $form = $this->getNewPasswordForm($activationCode);

        return $this->render('@FfjvFo/Security/newPassword.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function setNewPasswordAction(Request $request){

        $form = $this->getNewPasswordForm();
        $form->handleRequest($request);

        if($form->isValid()){
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('FfjvBoBundle:User')->findOneBy(array('email' => $data['email'], 'birthday' => $data['birthday'], 'activationCode' => $data['activationCode']));
            if($user){
                //set password
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $data['password']);
                $user->setPassword($encoded);
                $user->setActivationCode(base_convert(md5(uniqid(mt_rand(), true)), 16, 36));
                //persiste and flush
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', $this->get('translator')->trans("fo.global.noty.password_updated"));
                return $this->redirectToRoute('logout');
            }
        }
        return $this->redirectToRoute('login');
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendMailLostPasswordAction(Request $request){
        $translator = $this->get('translator');
        $type = 'error';
        $message = $translator->trans("fo.global.noty.error");

        $form = $this->getLostPasswordForm();
        $form->handleRequest($request);
        if($form->isValid()){

            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('FfjvBoBundle:User')->findOneBy(array('email' => $data['email']));
            if($user){
                $activationCode = base_convert(sha1(md5(uniqid(mt_rand(), true))), 16, 36);
                $user->setActivationCode($activationCode);
                //persiste and flush
                $em->persist($user);
                $em->flush();
                $this->sendMailToNewPassword($user, $activationCode);
                $type = 'success';
                $message = $translator->trans("fo.global.noty.email_send_reset_password");
            } else {
                $message = $translator->trans("fo.global.noty.email_not_exist");
            }
            $this->addFlash($type, $message);
            return $this->redirectToRoute('logout');
        }

        $this->addFlash($type, $message);

        return $this->redirectToRoute('login');
    }

    /* ****** PRIVATE ***************************************** */
    /**
     * @param string $activationCode
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getNewPasswordForm($activationCode = ''){

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ffjv_fo_security_set_new_password'))
            ->setMethod('POST')
            ->add('activationCode', HiddenType::class, array(
                'attr'  => array(
                    'value' => $activationCode
                )
            ))
            ->add('email', EmailType::class, array(
                'label' => 'Votre email:',
                'attr'  => array(
                    'class' => 'form-control'
                )
            ))
            ->add('birthday', BirthdayType::class, array(
                'label' => 'Votre date de naissance:',
                'attr' => array('class' => 'form-control-date')
            ))
            ->add('password', RepeatedType::class, array(
                'type'            => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options'         => array('required' => true),
                'first_options'   => array('label' => 'New Password'),
                'second_options'  => array('label' => 'Repeat New password'),
                'attr' => array('class' => 'form-control')
            ))
            ->add('submit', SubmitType::class, array('label' => 'Valider', 'attr' => array('class' => 'btn btn-sm btn-success')))
            ->getForm();
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    private function getLostPasswordForm(){

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ffjv_fo_security_send_mail_lostpassword'))
            ->setMethod('POST')
            ->add('email', EmailType::class, array(
                'label' => "fo.security.login.form.lost_password.label.email",
                'attr'  => array(
                    'class' => 'form-control'
                )
            ))
            ->add('submit', SubmitType::class, array('label' => "bo.form.contact.club.send", 'attr' => array('class' => 'btn btn-success')))
            ->getForm();
    }


    /**
     * @param int $userId
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getSendMailForm($userId){
        $form = $this->createForm(SendMailType::class, ['userId' => $userId], array(
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
        $registerForm = $this->createForm(CreateUserType::class, $user, array(
            'action' => $this->generateUrl('ffjv_fo_security_createuser'),
            'method' => 'POST',
            'registering' => true
        ));
        $registerForm->add('submit', SubmitType::class, array(
            'label' => 'enregistrer'
        ));
        return $registerForm;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    private function sendMailToConfirmation(User $user){

        $message = \Swift_Message::newInstance()
            ->setSubject('FFjv Email de confirmation')
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

    /**
     * @param User $user
     * @param $activationCode
     * @return bool
     */
    private function sendMailToNewPassword(User $user, $activationCode){

        $message = \Swift_Message::newInstance()
            ->setSubject('FFjv Nouveau mot de passe')
            ->setFrom('contact@ffjv.org')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView('@FfjvFo/Emails/newPassword.html.twig',
                    array('code' => $activationCode,
                        'user' => $user)
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        return true;
    }
}
