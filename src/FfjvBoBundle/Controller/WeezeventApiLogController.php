<?php

namespace FfjvBoBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FfjvBoBundle\Entity\WeezeventApiLog;
use FfjvBoBundle\Form\WeezeventApiLogType;

/**
 * WeezeventApiLog controller.
 *
 */
class WeezeventApiLogController extends Controller
{
    /**
     * Lists all WeezeventApiLog entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $weezeventApiLogs = $em->getRepository('FfjvBoBundle:WeezeventApiLog')->findAll();

        return $this->render('weezeventapilog/index.html.twig', array(
            'weezeventApiLogs' => $weezeventApiLogs,
        ));
    }

    /**
     * Creates a new WeezeventApiLog entity.
     *
     */
    public function newAction(Request $request)
    {
        $weezeventApiLog = new WeezeventApiLog();
        $form = $this->createForm(WeezeventApiLogType::class, $weezeventApiLog->toArray(), ['app_service' => $this->get('app_service')->setUser($this->getUser())])->add('submit', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $club = $em->getRepository('FfjvBoBundle:Clubs')->find($data['club_id']);
            if(!$club){
                throw new \Exception('Club unknown');
            }
            $weezeventApiLog = $this->getDoctrine()->getRepository('FfjvBoBundle:WeezeventApiLog')->findOneBy(array('club' => $club));
            if(!$weezeventApiLog){
                $weezeventApiLog = new WeezeventApiLog();
            }
            $weezeventApiLog->setClub($club);
            $weezeventApiLog->setUser($this->getUser());
            $weezeventApiLog->setApiPassword($data['api_password']);
            $weezeventApiLog->setApiKey($data['api_key']);
            $weezeventApiLog->setApiUsername($data['api_username']);

            if($this->get('weezeventapi')->setAuthAccess($data['api_username'], $data['api_password'], $data['api_key'])->initConnection()){
                $em->persist($weezeventApiLog);
                $em->flush();
                $this->addFlash('success', 'Connection successfully');
                return $this->redirectToRoute('clubs_show', array('id' => $club->getId()));
            }
        }
        $this->addFlash('error', 'Wrong loging submit');
        return $this->redirectToRoute('clubs_show', array('id' => $club->getId()));
    }

    /**
     * Finds and displays a WeezeventApiLog entity.
     *
     */
    public function showAction(WeezeventApiLog $weezeventApiLog)
    {
        $deleteForm = $this->createDeleteForm($weezeventApiLog);

        return $this->render('weezeventapilog/show.html.twig', array(
            'weezeventApiLog' => $weezeventApiLog,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing WeezeventApiLog entity.
     *
     */
    public function editAction(Request $request, WeezeventApiLog $weezeventApiLog)
    {
        $deleteForm = $this->createDeleteForm($weezeventApiLog);
        $editForm = $this->createForm('FfjvBoBundle\Form\WeezeventApiLogType', $weezeventApiLog);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($weezeventApiLog);
            $em->flush();

            return $this->redirectToRoute('weezeventapilog_edit', array('id' => $weezeventApiLog->getId()));
        }

        return $this->render('weezeventapilog/edit.html.twig', array(
            'weezeventApiLog' => $weezeventApiLog,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a WeezeventApiLog entity.
     *
     */
    public function deleteAction(Request $request, WeezeventApiLog $weezeventApiLog)
    {
        $form = $this->createDeleteForm($weezeventApiLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($weezeventApiLog);
            $em->flush();
        }

        return $this->redirectToRoute('weezeventapilog_index');
    }

    /**
     * Creates a form to delete a WeezeventApiLog entity.
     *
     * @param WeezeventApiLog $weezeventApiLog The WeezeventApiLog entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(WeezeventApiLog $weezeventApiLog)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('weezeventapilog_delete', array('id' => $weezeventApiLog->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
