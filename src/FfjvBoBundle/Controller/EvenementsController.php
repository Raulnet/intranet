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

        return $this->render('FfjvBoBundle:evenements:index.html.twig', array(
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
            if($evenement->getEndDate() > $evenement->getStartDate()){
                $em = $this->getDoctrine()->getManager();
                $evenement->setUser($this->getUser());
                $idZipCode = $this->get('evenements')->getIdZipCode($evenement->getZipCode());
                $evenement->setIdZipCode($idZipCode);
                $em->persist($evenement);
                $em->flush();
                $this->addFlash('success', 'l\\\'événement '.$evenement->getTitle().' a bien été créé');
                return $this->redirectToRoute('ffjv_bo_evenements_show', array('id' => $evenement->getId()));
            }
            $form->get('startDate')->addError('La date de fin doit être supérieur à la date de départ');

        }

        return $this->render('FfjvBoBundle:evenements:new.html.twig', array(
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
        return $this->render('FfjvBoBundle:evenements:show.html.twig', array(
            'evenement' => $evenement,
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
            $idZipCode = $this->get('evenements')->getIdZipCode($evenement->getZipCode());
            $evenement->setIdZipCode($idZipCode);
            $now = new \DateTime('now');
            $evenement->setLastUpdate($now);
            $em->persist($evenement);
            $em->flush();
            $this->addFlash('success', 'l\\\'événement '.$evenement->getTitle().' a bien été édité');
            return $this->redirectToRoute('ffjv_bo_evenements_show', array('id' => $evenement->getId()));
        }

        return $this->render('FfjvBoBundle:evenements:edit.html.twig', array(
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

        return $this->redirectToRoute('ffjv_bo_evenements_index');
    }

    /**
     * @param Clubs  $club
     * @param string $weezEvenementId
     *
     * @return string
     * @throws \Exception
     */
    public function getPagesWeezEvenementDetailAction(Clubs $club, $weezEvenementId = ''){

        if(!$club){
            throw new \Exception('This club not exist');
        }
        $events = $this->get('evenements')->getListWeezEvenement($club);
        $events = $events['events'];
        $indexOf = 0;
        foreach ($events as $key => $event){
            if($event['id'] == $weezEvenementId){
                $indexOf = $key;
            }
            $events[$key]['club_id'] = $club->getId();
            $events[$key]['template'] = false;
        }
        return $this->render('FfjvBoBundle:evenements:weez_evenement_detail.html.twig', [
            'club' => $club,
            'events' => $events,
            'selected' => $indexOf
        ]);
    }
    
    public function getWeezEvenementDetailAction(Request $request){
        $item = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $club = $em->getRepository('FfjvBoBundle:Clubs')->find($item['club_id']);
        if (!$club) {
            throw $this->createNotFoundException('Unable to find Clubs club.');
        }
        $eventDetail = $this->get('evenements')->getWeezEventDetail($club, $item['id']);
        $tickets = $this->get('evenements')->getWeezEventTickets($club, $item['id']);
        $item['last_update'] = $eventDetail['last_update'];
        $content = $this->renderView('FfjvBoBundle:evenements:_weez_event_details.html.twig', [
            'club' => $club,
            'eventTickets' => $tickets['events'][0],
            'event' => $eventDetail['events'],
            'last_update' => $eventDetail['last_update']
        ]);
        return new Response(json_encode(['template'=>$content, 'item'=> $item]), 200, ['Content-Type'=>'applcation/json']);
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
            ->setAction($this->generateUrl('ffjv_bo_evenements_delete', array('id' => $evenement->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
