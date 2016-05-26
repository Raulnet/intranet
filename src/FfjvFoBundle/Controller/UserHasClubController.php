<?php
/**
 * Created by PhpStorm.
 * User: laurentnegre
 * Date: 16/01/2016
 * Time: 18:28
 */
namespace FfjvFoBundle\Controller;

use FfjvBoBundle\Entity\Clubs;
use FfjvBoBundle\Entity\User;
use FfjvBoBundle\Entity\UserHasClubs;
use FfjvFoBundle\Form\MemberType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FfjvBoBundle\Entity\Messages;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserHasClubController extends Controller
{

    /**
     * @param int $membersHasClubId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editJoinRequestAction($membersHasClubId = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $userHasClubs = $em->getRepository('FfjvBoBundle:UserHasClubs')->find($membersHasClubId);
        $club = $em->getRepository('FfjvBoBundle:Clubs')->findOneBy(['id' => $userHasClubs->getClub()->getId()]);
        if ($userHasClubs->getRequestToJoin() == 0) {
            return $this->redirectToRoute('fo_clubs_show', ['clubId' => $club->getId()]);
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

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function setRequestJoinClubAction(Request $request)
    {

        $form = $this->getResponseRequestForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $request->request->get('form');
            if (array_key_exists('accepter', $data)) {
                return $this->acceptRequestJoinClub($data);
            }
            if (array_key_exists('supprimer', $data)) {
                return $this->deleteRequestJoinClub($data);
            }
            if (array_key_exists('refuser', $data)) {
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
    public function sendRequestToJoinAction(Request $request)
    {

        $form = $this->get('user_has_clubs')->getJoinClubForm('', array());
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $id = $data['club'];
            $club = $em->getRepository('FfjvBoBundle:Clubs')->find($id);
            if (!$club) {
                throw $this->createNotFoundException('Unable to find Clubs club.');
            }
            if ($this->addUserToRequestClub($club, $this->getUser())) {

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
                return $this->redirectToRoute('fo_clubs_show', array('clubId' => $club->getId()));
            }
        }
        return $this->redirectToRoute('ffjv_fo_home_index');
    }

    /**
     * @param int $memberId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editMemberAction($memberId = 0)
    {

        $em = $this->getDoctrine()->getManager();
        $member = $em->getRepository('FfjvBoBundle:UserHasClubs')->find($memberId);
        $club = $em->getRepository('FfjvBoBundle:Clubs')->findOneBy(['id' => $member->getClub()->getId()]);
        if (!$member) {
            return $this->redirectToRoute('fo_clubs_show', ['clubId' => $club->getId()]);
        }
        $countMembersActive = $this->get('clubs')->getCountMemberActive($club->getId());

        $form = $this->getFormUpdateMember($member);
        $formRemove = $this->getFormRemoveMember($memberId);

        return $this->render('FfjvFoBundle:UserHasClubs:editMember.html.twig', [
            'member' => $member,
            'club' => $club,
            'count_members' => $countMembersActive,
            'form' => $form->createView(),
            'form_remove' => $formRemove->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param int $memberId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function updateMemberAction(Request $request, $memberId = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $member = $em->getRepository('FfjvBoBundle:UserHasClubs')->find($memberId);

        $club = $em->getRepository('FfjvBoBundle:Clubs')->findOneBy(['id' => $member->getClub()->getId()]);
        
        $authorizationChecker = $this->get('security.authorization_checker');
        
        if (false === $authorizationChecker->isGranted('EDIT', $club)) {
            throw new AccessDeniedException();
        }
        //if club not esixt || user is not author
        if (!$club) {
           throw new \Exception('Club unknown');
        }
        //if member not existe
        if (!$member) {
            throw new \Exception('Member unknown');
        }
        $form = $this->getFormUpdateMember($member);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $member->setRoles($data->getRoles());
            $user = $member->getUser();
            $this->get('permissions')->setAcl($club, $user, $data->getPermissions());
            $em->persist($member);
            $em->flush();
            $this->addFlash('success', 'votre membre à bien été mis a jour');
            return $this->redirectToRoute('fo_clubs_show', ['clubId' => $club->getId()]);
        }
        $formRemove = $this->getFormRemoveMember($memberId);

        $countMembersActive = $this->get('clubs')->getCountMemberActive($club->getId());
        $this->addFlash('error', 'une erreur c\'est produite');
        return $this->render('FfjvFoBundle:UserHasClubs:editMember.html.twig', [
            'member' => $member,
            'club' => $club,
            'count_members' => $countMembersActive,
            'form' => $form->createView(),
            'form_remove' => $formRemove->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param $memberId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeMemberAction(Request $request, $memberId){

        $em = $this->getDoctrine()->getManager();
        $member = $em->getRepository('FfjvBoBundle:UserHasClubs')->find($memberId);
        if(!$member){
            $this->addFlash('error', 'une erreur c\'est produite !');
            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }
        $club = $em->getRepository('FfjvBoBundle:Clubs')->findOneBy(['id' => $member->getClub()->getId()]);
        //if club not esixt || user is not author
        if (!$club || $club->getUser() != $this->getUser()) {
            $this->addFlash('error', 'une erreur c\'est produite !');
            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }
        $form = $this->getFormRemoveMember($memberId);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->remove($member);
            $em->flush();
            $this->addFlash('success', 'votre membre à bien été mis a jour');
            return $this->redirectToRoute('fo_clubs_show', ['clubId' => $club->getId()]);
        }
        $this->addFlash('error', 'une erreur c\'est produite !');
        return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
    }

    /**
     * @param Clubs $club
     * @param User $user
     * @return bool
     */
    private function addUserToRequestClub(Clubs $club, User $user)
    {

        $em = $this->getDoctrine()->getManager();
        if ($em->getRepository('FfjvBoBundle:UserHasClubs')->findBy(array('user' => $user, 'club' => $club))) {
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
    private function getResponseRequestForm($userHasClubId = null)
    {
        $form = $this->createFormBuilder();
        $form->add('user_has_club', HiddenType::class, array(
            'attr' => array('value' => $userHasClubId)
        ));
        $form->add('message', TextType::class, array(
            'attr' => array()
        ));
        $form->add('accepter', SubmitType::class, array(
            'label' => 'accepter',
            'attr' => array('class' => 'btn btn-success')
        ));
        $form->add('refuser', SubmitType::class, array(
            'label' => 'refuser',
            'attr' => array('class' => 'btn btn-warning')
        ));
        $form->add('supprimer', SubmitType::class, array(
            'label' => 'supprimer',
            'attr' => array('class' => 'btn btn-danger')
        ));
        $form->setAction($this->generateUrl('fo_user_has_club_set_request', array('userHasClubId' => $userHasClubId)));
        $form->setMethod('POST');

        return $form->getForm();
    }

    /**
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function acceptRequestJoinClub(array $data)
    {
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
        $message->setSubject('Demande de rejoindre le club ' . $userHasClub->getClub() . ' acceptée');
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
    private function refuseRequestJoinClub(array $data)
    {
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
        $message->setSubject('Demande de rejoindre le club ' . $userHasClub->getClub() . ' refusée');
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
    private function deleteRequestJoinClub(array $data)
    {
        $em = $this->getDoctrine()->getManager();
        $userHasClub = $em->getRepository('FfjvBoBundle:UserHasClubs')->find($data['user_has_club']);
        $em->remove($userHasClub);
        $message = new Messages();
        $message->setAuthorUser($this->getUser());
        $message->setUser($userHasClub->getUser());
        $message->setEmail($userHasClub->getUser()->getEmail());
        $message->setMessage($data['message']);
        $message->setSubject('Demande de rejoindre le club ' . $userHasClub->getClub() . ' refusée');
        $em->persist($message);
        $em->flush();
        $this->get('contact')->sendRequestResponseToJoinCLub($message);

        $this->addFlash('success', 'La reponse a été envoyer');
        return $this->redirectToRoute('fo_clubs_show', array('clubId' => $userHasClub->getClub()->getId()));
    }

    /**
     * @param UserHasClubs $member
     * @return \Symfony\Component\Form\Form
     */
    private function getFormUpdateMember(UserHasClubs $member)
    {
        return $this->createForm(MemberType::class, $member, [
            "action" => $this->generateUrl('fo_user_has_club_update_member', ['memberId' => $member->getId()]),
            "method" => "PUT",
            "user_ace" => $this->get('permissions')->getPermission($member->getClub(), $this->getUser())
        ]);
    }

    /**
     * @param $memberId
     * @return \Symfony\Component\Form\Form
     */
    private function getFormRemoveMember($memberId)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('fo_user_has_club_remove_member', array('memberId' => $memberId)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'confirmer', 'attr'=> array('class' => 'btn btn-danger')))
            ->getForm();
    }
}