<?php

namespace Ffjv\FoBundle\Controller;

use Ffjv\BoBundle\Entity\Teams;
use Ffjv\BoBundle\Entity\UserHasClubs;
use Ffjv\BoBundle\Entity\User;
use Ffjv\BoBundle\Entity\UserHasTeams;
use Ffjv\FoBundle\Form\TeamMemberType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserHasTeamsController extends Controller
{
    /**
     * @param int $teamId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function selectMemberAction($teamId = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('FfjvBoBundle:Teams')->find($teamId);
        $club = $em->getRepository('FfjvBoBundle:Clubs')->find($team->getClub()->getId());
        $clubMembers = $club->getMembers();
            //todo trouver les utilisateur par team et les comparer avec les utilisateur membre du club pour affichier le select.
        //todo ou n'afficher que les utilisateur qui ne sont pas dans la team Voir
        $teamMembers = $team->getMembers();

        return $this->render('FfjvFoBundle:UserHasTeams:selectMember.html.twig', array(
            'team' => $team,
            'clubMembers' => $clubMembers,
            'teamMembers' => $teamMembers,
         ));
    }

    /**
     * @param int $clubMemberId
     * @param int $teamId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setMemberTeamAction($clubMemberId = 0, $teamId = 0){
        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('FfjvBoBundle:Teams')->find($teamId);
        $club = $em->getRepository('FfjvBoBundle:Clubs')->find($team->getClub()->getId());
        $clubMember = $em->getRepository('FfjvBoBundle:UserHasClubs')->find($clubMemberId);
        if($clubMember->getClub() != $club){
            $this->addFlash('error', 'une erreur \'est produite');
        }
        if($club->getUser() != $this->getUser()){
            $this->addFlash('error', 'une erreur \'est produite');
        }
        $form = $this->getFormMemberTeam($clubMember->getUser(), $teamId);


        return $this->render('FfjvFoBundle:UserHasTeams:setMember.html.twig', array(
            'team' => $team,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param int $teamId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addMemberAction(Request $request, $teamId = 0){
        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('FfjvBoBundle:Teams')->find($teamId);
        $club = $em->getRepository('FfjvBoBundle:Clubs')->find($team->getClub()->getId());
        if($team->getClub() != $club){
            $this->addFlash('error', 'une erreur \'est produite');
            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }
        if($club->getUser() != $this->getUser()){
            $this->addFlash('error', 'une erreur \'est produite');
            return $this->redirectToRoute('fo_clubs_show', array('clubId' => $club->getId()));
        }
        $form = $this->getFormMemberTeam(null, $teamId);
        $form->handleRequest($request);
        if($form->isValid()){
            $data = $form->getData();
            $userHasTeams = new UserHasTeams();
            $userHasTeams->setTeam($team);
            $userHasTeams->setRoles($data['roles']);
            $user = $em->getRepository('FfjvBoBundle:User')->find($data['user_id']);
            $userHasTeams->setUser($user);
            $em->persist($userHasTeams);
            $em->flush();
            $this->addFlash('success', 'Votre membre a bien été ajouter à la team '.$team->getTitle().' .');
        }

        return $this->redirectToRoute('fo_teams_show', array('teamId' => $team->getId()));
    }

    /**
     * @param User|null $user
     * @param $teamId
     * @return \Symfony\Component\Form\Form
     */
    private function getFormMemberTeam(User $user = null, $teamId){
        return $this->createForm(new TeamMemberType(), array('user_id' => ($user != null ? $user->getId() : null)), [
           'method' => 'POST',
            'action' => $this->generateUrl('fo_user_has_teams_add_member', ['teamId' => $teamId])
        ]);
    }

}
