<?php

namespace FfjvFoBundle\Controller;

use FfjvFoBundle\Form\UpdateProfilePasswordType;
use FfjvFoBundle\Form\UpdateProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use FfjvBoBundle\Entity\User;

/**
 * Class ProfileController
 * @package Ffjv\FoBundle\Controller
 */
class ProfileController extends Controller
{
    /**
     * @param $userUsername
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction($userUsername)
    {
        //if user is not user connected
        if($userUsername != $this->getUser()->getUsername()){
            return $this->redirectToRoute('ffjv_fo_home_index');
        }
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('user')->findOneByUsername($userUsername);
        $clubsMember = $em->getRepository('FfjvBoBundle:UserHasClubs')->getCLubMemberByUser($this->getUser());

        return $this->render('@FfjvFo/Profile/show.html.twig', array(
            'user' => $user,
            'clubsMember' => $clubsMember
        ));
    }

    /**
     * @param $userUsername
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editProfileAction($userUsername){

        //if user is not user connected
        if($userUsername != $this->getUser()->getUsername()){
            return $this->redirectToRoute('ffjv_fo_home_index');
        }
        $user = $this->get('user')->findOneByUsername($userUsername);

        $editForm = $this->getEditForm($user);
        $deleteForm = $this->getDeleteProfileForm($userUsername);

        return $this->render('@FfjvFo/Profile/edit.html.twig', array(
            'editForm'      => $editForm->createView(),
            'deleteForm'    => $deleteForm->createView(),
            'user'          => $user
        ));
    }

    /**
     * @param Request $request
     * @param string  $userUsername
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateProfileAction(Request $request, $userUsername){

        if($userUsername != $this->getUser()->getUsername()){
            $this->addFlash('error', 'Une erreur c\'est produite, le profile n\'as pue être mis à jours');
            return $this->redirectToRoute('ffjv_fo_home_index');
        }

        $user = $this->get('user')->findOneByUsername($userUsername);
        $editForm = $this->getEditForm($user);
        $editForm->handleRequest($request);
        if($editForm->isValid()){
            $em = $this->getDoctrine()->getManager();
            //set zipcode
            $user->setIdZipCode($this->get('user')->getIdZipCode($user->getZipCode(), $user->getNationality()));
            //persiste and flush
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre profile a bien été mis à jours');
            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $user->getUsername()));
        }

        $this->addFlash('error', 'Une erreur c\'est produite, le profile n\'as pue être mis à jours');
        return $this->redirectToRoute('fo_profile_show', array('username' => $user->getUsername()));
    }

    /**
     * @param $userUsername
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editProfilePasswordAction($userUsername){

        //if user is not user connected
        if($userUsername != $this->getUser()->getUsername()){
            return $this->redirectToRoute('ffjv_fo_home_index');
        }
        $user = $this->get('user')->findOneByUsername($userUsername);

        $editForm = $this->getEditPasswordForm($user);

        return $this->render('@FfjvFo/Profile/editPassword.html.twig', array(
            'form'          => $editForm->createView(),
            'user'          => $user
        ));
    }

    /**
     * @param Request $request
     * @param string  $userUsername
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateProfilePasswordAction(Request $request, $userUsername){

        if($userUsername != $this->getUser()->getUsername()){
            $this->addFlash('error', 'Une erreur c\'est produite, le profile n\'as pue être mis à jours');
            return $this->redirectToRoute('ffjv_fo_home_index');
        }

        $user = $this->get('user')->findOneByUsername($userUsername);
        $editForm = $this->getEditPasswordForm($user);
        $editForm->handleRequest($request);
        if($editForm->isValid()){
            $em = $this->getDoctrine()->getManager();
            //set password
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);

            //persiste and flush
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre mot de passe a bien été mis à jours');
            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $user->getUsername()));
        }

        $this->addFlash('error', 'Une erreur c\'est produite, le profile n\'as pue être mis à jours');
        return $this->redirectToRoute('fo_profile_show', array('username' => $user->getUsername()));
    }

    /**
     * @param Request $request
     * @param string  $userUsername
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProfileAction(Request $request, $userUsername)
    {
        if ($userUsername != $this->getUser()->getUsername()) {
            $this->addFlash('error', 'Une erreur c\'est produite ?!');

            return $this->redirect($this->generateUrl('logout'));
        }
        $form = $this->getDeleteProfileForm($userUsername);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $user = $this->get('user')->findOneByUsername($userUsername);
            if (!$user) {
                throw $this->createNotFoundException('Unable to find User user.');
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Vos données bien été supprimées');

            $this->get('session')->remove('user');
            $this->get('session')->clear();

            return $this->redirectToRoute('ffjv_fo_home_index');
        }

        return $this->redirect($this->generateUrl('logout'));
    }

    /* ****** METHOD ***************************************** */
    /**
     * @param User $user
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getEditForm(User $user){
        $editForm = $this->createForm(UpdateProfileType::class, $user, array(
            'action' => $this->generateUrl('fo_profile_update', array('userUsername' => $user->getUsername()))
        ));

        $editForm->add('submit', SubmitType::class, array(
            'label' => 'EDITER'
        ));
        return $editForm;
    }

    /**
     * @param User $user
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getEditPasswordForm(User $user){
        $editPasswordForm = $this->createForm(UpdateProfilePasswordType::class, $user, array(
            'action' => $this->generateUrl('fo_profile_update_password', array('userUsername' => $user->getUsername()))
        ));

        $editPasswordForm->add('submit', SubmitType::class, array(
            'label' => 'EDITER',
        ));
        return $editPasswordForm;
    }

    /**
     * @param string $userName
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getDeleteProfileForm($userName){

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('fo_profile_delete', array('userUsername' => $userName)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Confirmer', 'attr' => array('class' => 'btn btn-sm btn-danger')))
            ->getForm()
            ;
    }
}
