<?php
/**
 * Created by PhpStorm.
 * User: laurentnegre
 * Date: 16/01/2016
 * Time: 18:28
 */
namespace Ffjv\FoBundle\Controller;

use Ffjv\BoBundle\Entity\Clubs;
use Ffjv\BoBundle\Entity\User;
use Ffjv\BoBundle\Entity\UserHasClubs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ffjv\BoBundle\Entity\Messages;

class UserHasClubController extends Controller
{

    /**
     * @param int $membersHasClubId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editJoinRequestAction($membersHasClubId = 0){
        $em = $this->getDoctrine()->getManager();
        $userHasClubs = $em->getRepository('FfjvBoBundle:UserHasClubs')->find($membersHasClubId);
        $club = $em->getRepository('FfjvBoBundle:Clubs')->findOneBy(['id' => $userHasClubs->getClub()->getId()]);
        if($userHasClubs->getRequestToJoin() == 0){
            return $this->redirectToRoute('fo_clubs_show', ['clubTitle' => $club->getTitle()]);
        }
        $countMembersActive = $this->get('clubs')->getCountMemberActive($club->getId());
        $message = $em->getRepository('FfjvBoBundle:Messages')->getLastRequestJoinClubByUser($club, $userHasClubs->getUser());
        $form = $this->getResponseRequestForm($membersHasClubId);

        return $this->render('FfjvFoBundle:UserHasClubs:editJoinRequest.html.twig', [
            'club' => $club,
            'count_members' => $countMembersActive,
            'message' => $message,
            'form' => $form->createView()
        ]);
    }

    public function setRequestJoinClubAction(Request $request){

        $form = $this->getResponseRequestForm();
        $form->handleRequest($request);
        if($form->isValid()){
            $data = $request->request->get('form');
            if(array_key_exists('accepter', $data)){
                return $this->acceptRequestJoinClub($data);
            }
            if(array_key_exists('supprimer', $data)){
                return $this->deleteRequestJoinClub($data);
            }
            if(array_key_exists('refuser', $data)){
                return $this->refuseRequestJoinClub($data);
            }
        }
        $this->addFlash('error', 'une erreur c\'est produite !');
        return $this->redirectToRoute('fo_profile_show', array('userUsername', $this->getUser()->getUsername()));

    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function sendRequestToJoinAction(Request $request){

        $form = $this->get('user_has_clubs')->getJoinClubForm('', array());
        $form->handleRequest($request);
        if($form->isValid()){
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $id = $data['club'];
            $club = $em->getRepository('FfjvBoBundle:Clubs')->find($id);
            if (!$club) {
                throw $this->createNotFoundException('Unable to find Clubs club.');
            }
            if($this->addUserToRequestClub($club, $this->getUser())) {


                // create and save Mesage
                $message = new Messages();
                $message->setAuthorUser($this->getUser());
                $message->setMessage($data['content']);
                $message->setSubject('join club');
                $message->setClub($club);
                $message->setEmail($club->getEmail());
                $message->setType(Messages::REQUEST_JOIN_CLUB);
                $em->persist($message);
                $em->flush();
                //send message
                if ($this->get('contact')->sendRequestToJoinCLub($message)) {
                    $this->addFlash('success', 'Votre message a bien été envoyée');
                } else {
                    $this->addFlash('error', 'Une erreur c\'est produite ! Votre message n\'a pus être envoyé .');
                }
                return $this->redirectToRoute('fo_clubs_show', array('clubTitle' => $club->getTitle()));
            }
        }
        return $this->redirectToRoute('ffjv_fo_home_index');
    }

    /**
     * @param Clubs $club
     * @param User $user
     * @return bool
     */
    private function addUserToRequestClub(Clubs $club, User $user){

        $em = $this->getDoctrine()->getManager();
        if($em->getRepository('FfjvBoBundle:UserHasClubs')->findBy(array('user' => $user, 'club' => $club))){
            $this->addFlash('error', 'Votre demande n\'a pus aboutir, vous avez déjà fait une requête à ce club');
            return false;
        }
        $userHasClub = new UserHasClubs();
        $userHasClub->setUser($user);
        $userHasClub->setClub($club);
        $userHasClub->setRoles(array('ROLES_REQUEST_TO_JOIN'));
        $userHasClub->setRequestToJoin(2);
        $em->persist($userHasClub);
        $em->flush();
        return true;
    }

    /**
     * @param $userHasClubId
     * @return \Symfony\Component\Form\Form
     */
    private function getResponseRequestForm($userHasClubId = null){
        $form = $this->createFormBuilder();
        $form->add('user_has_club', 'hidden', array(
            'attr' => array('value' => $userHasClubId)
        ));
        $form->add('message', 'text', array(
            'attr' => array()
        ));
        $form->add('accepter', 'submit', array(
            'label' => 'accepter',
            'attr'  => array('class' => 'btn btn-success')
        ));
        $form->add('refuser', 'submit', array(
            'label' => 'refuser',
            'attr'  => array('class' => 'btn btn-warning')
        ));
        $form->add('supprimer', 'submit', array(
            'label' => 'supprimer',
            'attr'  => array('class' => 'btn btn-danger')
        ));
        $form->setAction($this->generateUrl('fo_user_has_club_set_request', array('userHasClubId' => $userHasClubId)));
        $form->setMethod('POST');

        return $form->getForm();
    }

    /**
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function acceptRequestJoinClub(array $data){
        $em = $this->getDoctrine()->getManager();
        $userHasClub = $em->getRepository('FfjvBoBundle:UserHasClubs')->find($data['user_has_club']);
        $userHasClub->setRoles(array('ROLE_MEMBER'));
        $userHasClub->setRequestToJoin(0);
        $userHasClub->setLastUpdate(new \DateTime('now'));
        $em->persist($userHasClub);
        $em->flush();
        $message = new Messages();
        $message->setAuthorUser($this->getUser());
        $message->setUser($userHasClub->getUser());
        $message->setEmail($userHasClub->getUser()->getEmail());
        $message->setMessage($data['message']);
        $message->setSubject('Demande de rejoindre le club '.$userHasClub->getClub().' acceptée' );
        $em->persist($message);
        $em->flush();
        $this->get('contact')->sendRequestResponseToJoinCLub($message);

        $this->addFlash('success', 'La reponse a été envoyer');
        return $this->redirectToRoute('fo_clubs_show', array('clubId' => $userHasClub->getClub()->getId()));
    }

    /**
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function refuseRequestJoinClub(array $data){
        $em = $this->getDoctrine()->getManager();
        $userHasClub = $em->getRepository('FfjvBoBundle:UserHasClubs')->find($data['user_has_club']);
        $userHasClub->setRequestToJoin(1);
        $userHasClub->setLastUpdate(new \DateTime('now'));
        $em->persist($userHasClub);
        $em->flush();
        $message = new Messages();
        $message->setAuthorUser($this->getUser());
        $message->setUser($userHasClub->getUser());
        $message->setEmail($userHasClub->getUser()->getEmail());
        $message->setMessage($data['message']);
        $message->setSubject('Demande de rejoindre le club '.$userHasClub->getClub().' refusée' );
        $em->persist($message);
        $em->flush();
        $this->get('contact')->sendRequestResponseToJoinCLub($message);

        $this->addFlash('success', 'La reponse a été envoyer');
        return $this->redirectToRoute('fo_clubs_show', array('clubId' => $userHasClub->getClub()->getId()));
    }

    /**
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function deleteRequestJoinClub(array $data){
        $em = $this->getDoctrine()->getManager();
        $userHasClub = $em->getRepository('FfjvBoBundle:UserHasClubs')->find($data['user_has_club']);
        $em->remove($userHasClub);
        $message = new Messages();
        $message->setAuthorUser($this->getUser());
        $message->setUser($userHasClub->getUser());
        $message->setEmail($userHasClub->getUser()->getEmail());
        $message->setMessage($data['message']);
        $message->setSubject('Demande de rejoindre le club '.$userHasClub->getClub().' refusée' );
        $em->persist($message);
        $em->flush();
        $this->get('contact')->sendRequestResponseToJoinCLub($message);

        $this->addFlash('success', 'La reponse a été envoyer');
        return $this->redirectToRoute('fo_clubs_show', array('clubId' => $userHasClub->getClub()->getId()));
    }
}