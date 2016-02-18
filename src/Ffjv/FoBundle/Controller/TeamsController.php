<?php

namespace Ffjv\FoBundle\Controller;

use Ffjv\BoBundle\Entity\Teams;
use Ffjv\BoBundle\Form\TeamsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TeamsController extends Controller
{
    /**
     * @param $teamId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($teamId)
    {
        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('FfjvBoBundle:Teams')->find($teamId);
        $editForm = $this->getFormTeamEdit($team);
        $deleteForm = $this->getDeleteForm($team);
        return $this->render('FfjvFoBundle:Teams:show.html.twig', [
            'team' => $team,
            'editForm' => $editForm->createView(),
            'deleteForm' => $deleteForm->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param int $teamId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $teamId = 0){
        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('FfjvBoBundle:Teams')->find($teamId);
        if(!$team){
            $this->addFlash('error', 'une erreur c\est produite . Cette team n\'existe pas');
            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }
        if($team->getClub()->getUser() != $this->getUser()){
            $this->addFlash('error', 'une erreur c\est produite . Vous n\'êtes pas autorisez à modifier cette equipe .' );
            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }
        $editForm = $this->getFormTeamEdit($team);
        $editForm->handleRequest($request);
        $deleteForm = $this->getDeleteForm($team);
        if($editForm->isValid()){
            $em->persist($team);
            $em->flush();
            $this->addFlash('success', 'Votre Team a été mise a jours !');
        } else {
            $this->addFlash('error', 'une erreur c\est produite . Votre team n\'a pus être mise à jour.');
        }
        return $this->render('FfjvFoBundle:Teams:show.html.twig', [
            'team'          => $team,
            'editForm'      => $editForm->createView(),
            'deleteForm'    => $deleteForm->createView()
        ]);
    }

    public function deleteAction(Request $request, $teamId = 0){
        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('FfjvBoBundle:Teams')->find($teamId);
        if(!$team){
            $this->addFlash('error', 'une erreur c\est produite . Cette team n\'existe pas');
            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }
        if($team->getClub()->getUser() != $this->getUser()){
            $this->addFlash('error', 'une erreur c\est produite . Vous n\'êtes pas autorisez à modifier cette equipe .' );
            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }
        $deleteForm = $this->getDeleteForm($team);
        $deleteForm->handleRequest($request);
        if($deleteForm->isValid()){
            $em->remove($team);
            $em->flush();
            $this->addFlash('success', 'Votre Team a bien été supprimée !');
        } else {
            $this->addFlash('error', 'une erreur c\est produite . Votre team n\'a pus être supprimée.');
        }
        return $this->redirectToRoute('fo_clubs_show', ['clubId' => $team->getClub()->getId()]);
    }

    /**
     * @param Teams $team
     * @return $this|\Symfony\Component\Form\FormInterface
     */
    private function getFormTeamEdit(Teams $team){
        return $this->createForm(new TeamsType(), $team, [
            "method" => "PUT",
            "action" => $this->generateUrl("fo_teams_update", ["teamId" => $team->getId()])
        ])->add("submit", "submit", [
            "label" => "enregistrer",
            "attr" => [
                "class" => "btn btn-success"
            ]
        ]);
    }

    /**
     * @param Teams $team
     * @return \Symfony\Component\Form\Form
     */
    private function getDeleteForm(Teams $team){

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('fo_teams_delete', array('teamId' => $team->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', [
                'label' => 'Confirmer',
                'attr'  => ["class" => "btn btn-danger"]
            ])
            ->getForm();

    }
}
