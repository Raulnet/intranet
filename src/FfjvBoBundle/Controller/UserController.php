<?php

namespace FfjvBoBundle\Controller;

use FfjvBoBundle\Form\CreateUserType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FfjvBoBundle\Entity\User;
use FfjvBoBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * User controller.
 *
 */
class UserController extends Controller
{

    /**
     * Lists all User users.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
   


        $users = $em->getRepository('FfjvBoBundle:User')->getAllUser();

        return $this->render('FfjvBoBundle:User:index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->getCreateUserForm($user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            // encode password
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            // create unique licence
            $licence = $this->get('licences_users')->getNewLicences($user);
            $user->setLicence($licence);
            // persist entity and flush it
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $user->getId())));
        }
        return $this->render('FfjvBoBundle:User:new.html.twig', array(
            'user' => $user,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @param User $user
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getCreateUserForm(User $user){

        $form = $this->createForm(CreateUserType::class, $user, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));
        $form->add('submit', SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new User user.
     *
     */
    public function newAction()
    {
        $user = new User();
        $form   = $this->getCreateUserForm($user);

        return $this->render('FfjvBoBundle:User:new.html.twig', array(
            'user' => $user,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('FfjvBoBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User user.');
        }

        $editForm = $this->createEditForm($user);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FfjvBoBundle:User:show.html.twig', array(
            'user'      => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Displays a form to edit an existing User user.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('FfjvBoBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User user.');
        }

        $editForm = $this->createEditForm($user);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FfjvBoBundle:User:edit.html.twig', array(
            'user'      => $user,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a User user.
    *
    * @param User $user The user
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $user)
    {
        $form = $this->createForm(UserType::class, $user, array(
            'action' => $this->generateUrl('user_update', array('id' => $user->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing User user.
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('FfjvBoBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User user.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($user);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $user->setLastUpdate(new \DateTime('now'));
            $em->flush();

            $this->addFlash('success', "l'utilisateur ". $user->getUsername()  ."a bien été mis a jours");
            return $this->redirect($this->generateUrl('user_show', array('id' => $id)));
        }

        return $this->render('FfjvBoBundle:User:edit.html.twig', array(
            'user'      => $user,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a User user.
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('FfjvBoBundle:User')->find($id);

            if (!$user) {
                throw $this->createNotFoundException('Unable to find User user.');
            }

            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'L\'utilisateur '.$user->getFirstname().' '. $user->getLastname()  .' à bien été supprimé');
        }

        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * Creates a form to delete a User user by id.
     *
     * @param mixed $id The user id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Confirmer', 'attr' => array('class' => 'btn btn-danger ')))
            ->getForm()
        ;
    }
}
