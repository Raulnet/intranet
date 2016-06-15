<?php

namespace FfjvBoBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FfjvBoBundle\Entity\Teams;
use FfjvBoBundle\Form\TeamsType;

/**
 * Teams controller.
 *
 */
class TeamsController extends Controller
{

    /**
     * Lists all Teams entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $teams = $em->getRepository('FfjvBoBundle:Teams')->findAll();

        return $this->render('FfjvBoBundle:Teams:index.html.twig', array(
            'teams' => $teams,
        ));
    }

    /**
     * @param Request $request
     * @param int     $clubId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, $clubId)
    {
        $team = new Teams();
        $form = $this->createCreateForm($team, $clubId);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $club = $this->get('clubs')->findOneById($clubId);
            $licence = $this->get('licences_teams')->getNewLicences($club);
            $team->setClub($club);
            $team->setUser($this->getUser());
            $team->setLicence($licence);

            $em->persist($team);
            $em->flush();

            return $this->redirect($this->generateUrl('teams_show', array('id' => $team->getId())));
        }

        return $this->render('FfjvBoBundle:Teams:new.html.twig', array(
            'entity' => $team,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @param Teams $entity
     * @param int   $clubId
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(Teams $entity, $clubId)
    {
        $form = $this->createForm(TeamsType::class, $entity, array(
            'action' => $this->generateUrl('teams_create', array('clubId' => $clubId)),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Create', 'attr' => array('class' => 'btn btn-success')));

        return $form;
    }

    /**
     * @param $clubId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction($clubId)
    {
        $team = new Teams();
        $club = $this->get('clubs')->findOneById($clubId);
        $team->setClub($club);
        $team->setUser($this->getUser());
        $form   = $this->createCreateForm($team, $clubId);

        return $this->render('FfjvBoBundle:Teams:new.html.twig', array(
            'team' => $team,
            'club' => $club,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Teams entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $team = $em->getRepository('FfjvBoBundle:Teams')->find($id);

        if (!$team) {
            throw $this->createNotFoundException('Unable to find Teams entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FfjvBoBundle:Teams:show.html.twig', array(
            'team'      => $team,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Teams entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FfjvBoBundle:Teams')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Teams entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FfjvBoBundle:Teams:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Teams entity.
    *
    * @param Teams $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Teams $entity)
    {
        $form = $this->createForm(TeamsType::class, $entity, array(
            'action' => $this->generateUrl('teams_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Teams entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FfjvBoBundle:Teams')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Teams entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('teams_edit', array('id' => $id)));
        }

        return $this->render('FfjvBoBundle:Teams:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Teams entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FfjvBoBundle:Teams')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Teams entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('teams'));
    }

    /**
     * Creates a form to delete a Teams entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('teams_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
