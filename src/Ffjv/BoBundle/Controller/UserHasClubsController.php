<?php

namespace Ffjv\BoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Ffjv\BoBundle\Entity\UserHasClubs;
use Ffjv\BoBundle\Form\UserHasClubsType;

/**
 * UserHasClubs controller.
 *
 */
class UserHasClubsController extends Controller
{

    /**
     * Lists all UserHasClubs entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FfjvBoBundle:UserHasClubs')->findAll();

        return $this->render('FfjvBoBundle:UserHasClubs:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new UserHasClubs entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new UserHasClubs();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('userhasclubs_show', array('id' => $entity->getId())));
        }

        return $this->render('FfjvBoBundle:UserHasClubs:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a UserHasClubs entity.
     *
     * @param UserHasClubs $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(UserHasClubs $entity)
    {
        $form = $this->createForm(new UserHasClubsType(), $entity, array(
            'action' => $this->generateUrl('userhasclubs_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new UserHasClubs entity.
     *
     */
    public function newAction()
    {
        $entity = new UserHasClubs();
        $form   = $this->createCreateForm($entity);

        return $this->render('FfjvBoBundle:UserHasClubs:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a UserHasClubs entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FfjvBoBundle:UserHasClubs')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserHasClubs entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FfjvBoBundle:UserHasClubs:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing UserHasClubs entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FfjvBoBundle:UserHasClubs')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserHasClubs entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FfjvBoBundle:UserHasClubs:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a UserHasClubs entity.
    *
    * @param UserHasClubs $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(UserHasClubs $entity)
    {
        $form = $this->createForm(new UserHasClubsType(), $entity, array(
            'action' => $this->generateUrl('userhasclubs_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing UserHasClubs entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FfjvBoBundle:UserHasClubs')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserHasClubs entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('userhasclubs_edit', array('id' => $id)));
        }

        return $this->render('FfjvBoBundle:UserHasClubs:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a UserHasClubs entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FfjvBoBundle:UserHasClubs')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find UserHasClubs entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('userhasclubs'));
    }

    /**
     * Creates a form to delete a UserHasClubs entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('userhasclubs_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}