<?php

namespace FfjvBoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use FfjvBoBundle\Entity\Evenements;
use FfjvBoBundle\Form\EvenementsType;
use FfjvBoBundle\Entity\Clubs;

/**
 * Evenements controller.
 *
 */
class EvenementsController extends Controller
{
    /**
     * Lists all Evenements entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $evenements = $em->getRepository('FfjvBoBundle:Evenements')->findAll();

        return $this->render('evenements/index.html.twig', array(
            'evenements' => $evenements,
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getEventClubAction(Request $request)
    {
        $item = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $club = $em->getRepository('FfjvBoBundle:Clubs')->find($item['club_id']);
        if (!$club) {
            throw $this->createNotFoundException('Unable to find Clubs club.');
        }
        $events = $this->get('evenements')->getListEventClub($club);
        $content = $this->renderView('FfjvBoBundle:evenements:_board.html.twig', [
            'events' => $events,
            'club' => $club
        ]);
        return new Response(json_encode(['template'=>$content, 'item'=> $item]), 200, ['Content-Type'=>'applcation/json']);
    }

    /**
     * Creates a new Evenements entity.
     *
     */
    public function newAction(Request $request)
    {
        $evenement = new Evenements();
        $form = $this->createForm('FfjvBoBundle\Form\EvenementsType', $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($evenement);
            $em->flush();

            return $this->redirectToRoute('evenements_show', array('id' => $evenement->getId()));
        }

        return $this->render('evenements/new.html.twig', array(
            'evenement' => $evenement,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Evenements entity.
     *
     */
    public function showAction(Evenements $evenement)
    {
        $deleteForm = $this->createDeleteForm($evenement);

        return $this->render('evenements/show.html.twig', array(
            'evenement' => $evenement,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Evenements entity.
     *
     */
    public function editAction(Request $request, Evenements $evenement)
    {
        $deleteForm = $this->createDeleteForm($evenement);
        $editForm = $this->createForm('FfjvBoBundle\Form\EvenementsType', $evenement);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($evenement);
            $em->flush();

            return $this->redirectToRoute('evenements_edit', array('id' => $evenement->getId()));
        }

        return $this->render('evenements/edit.html.twig', array(
            'evenement' => $evenement,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Evenements entity.
     *
     */
    public function deleteAction(Request $request, Evenements $evenement)
    {
        $form = $this->createDeleteForm($evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($evenement);
            $em->flush();
        }

        return $this->redirectToRoute('evenements_index');
    }

    /**
     * Creates a form to delete a Evenements entity.
     *
     * @param Evenements $evenement The Evenements entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Evenements $evenement)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('evenements_delete', array('id' => $evenement->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
