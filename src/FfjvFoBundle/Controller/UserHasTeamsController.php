<?php

namespace FfjvFoBundle\Controller;

use FfjvBoBundle\Entity\Teams;
use FfjvBoBundle\Entity\UserHasClubs;
use FfjvBoBundle\Entity\User;
use FfjvBoBundle\Entity\UserHasTeams;
use FfjvFoBundle\Form\TeamMemberType;
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

        $member = new UserHasTeams();
        $form = $this->getFormMemberTeam($clubMember->getUser(), $teamId, $member);

        return $this->render('FfjvFoBundle:UserHasTeams:setMember.html.twig', array(
            'team' => $team,
            'form' => $form->createView()
        ));
    }

    /**
     * @param $memberId
     * @param $teamId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editMemberAction($memberId, $teamId){

        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('FfjvBoBundle:Teams')->find($teamId);
        $member = $em->getRepository('FfjvBoBundle:UserHasTeams')->find($memberId);
        if($member->getTeam() != $team){
            $this->addFlash('error', 'une erreur \'est produite avec la team');
            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }
        $club = $em->getRepository('FfjvBoBundle:Clubs')->find($team->getClub()->getId());
        if($club->getUser() != $this->getUser()){
            $this->addFlash('error', 'une erreur \'est produite avec le club');
            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }
        $form = $this->getFormMemberTeam($member->getUser(), $teamId, $member);


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
    public function updateMemberAction(Request $request, $teamId = 0){
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
        $member = new UserHasTeams();
        $form = $this->getFormMemberTeam(null, $teamId, $member);
        $form->handleRequest($request);

        if($form->isValid()){
            $data = $form->getData();
            $user = $em->getRepository('FfjvBoBundle:User')->find($data['user_id']);
            $member = $em->getRepository('FfjvBoBundle:UserHasTeams')->findBy(['user' => $user, 'team' => $team]);
            if($member){
                $userHasTeams = $member;
                $userHasTeams->setRoles($data['roles']);
                
                $em->persist($userHasTeams);
                $em->flush();
                $this->addFlash('success', 'Votre membre a bien été éditer à la team '.$team->getTitle().' .');
            } else {
                $this->addFlash('error', 'Ce membre n\'est pas enregistré dans la team '.$team->getTitle().' .');
            }
        }

        return $this->redirectToRoute('fo_teams_show', array('teamId' => $team->getId()));
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
        $member = new UserHasTeams();

        $form = $this->getFormMemberTeam(null, $teamId, $member);
        $form->handleRequest($request);
        if($form->isValid()){
            $data = $form->getData();
            $user = $em->getRepository('FfjvBoBundle:User')->find($data['user_id']);
            $member = $em->getRepository('FfjvBoBundle:UserHasTeams')->findBy(['user' => $user, 'team' => $team]);
            if(!$member){
                $userHasTeams = new UserHasTeams();
                $userHasTeams->setTeam($team);
                $userHasTeams->setRoles($data['roles']);

                $userHasTeams->setUser($user);
                $em->persist($userHasTeams);
                $em->flush();
                $this->addFlash('success', 'Votre membre a bien été ajouté à la team '.$team->getTitle().' .');
            } else {
                $this->addFlash('error', 'Ce membre est déjà enregistré dans la team '.$team->getTitle().' .');
            }
        }

        return $this->redirectToRoute('fo_teams_show', array('teamId' => $team->getId()));
    }

    /**
     * @param $memberId
     * @param $teamId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeMemberAction($memberId, $teamId){
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
        $member = $em->getRepository('FfjvBoBundle:UserHasTeams')->find($memberId);
        if($member->getTeam() == $team){
            $em->remove($member);
            $em->flush();
            $this->addFlash('success', 'Votre membre a bien été retiré de la team '.$team->getTitle().' .');
        }
        return $this->redirectToRoute('fo_teams_show', array('teamId' => $team->getId()));
    }

    /**
     * @param User|null $user
     * @param int   $teamId
     * @param UserHasTeams $userHasTeams
     * @param string $url
     * @return \Symfony\Component\Form\Form
     */
    private function getFormMemberTeam(User $user = null, $teamId, UserHasTeams $userHasTeams, $url = ''){
        if($url == ""){
            $url = $this->generateUrl('fo_user_has_teams_add_member', ['teamId' => $teamId]);
        }
        return $this->createForm(TeamMemberType::class, array('user_id' => ($user != null ? $user->getId() : null), 'roles' => $userHasTeams->getRoles()), [
           'method' => 'POST',
            'action' => $url
        ]);
    }

}
