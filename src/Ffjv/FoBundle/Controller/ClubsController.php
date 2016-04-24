<?php
namespace Ffjv\FoBundle\Controller;

use Ffjv\BoBundle\Entity\Clubs;
use Ffjv\BoBundle\Entity\Teams;
use Ffjv\BoBundle\Entity\UserHasClubs;
use Ffjv\BoBundle\Form\ClubsType;
use Ffjv\BoBundle\Form\ContactClubType;
use Ffjv\BoBundle\Entity\Messages;
use Ffjv\BoBundle\Form\JoinClubType;
use Ffjv\BoBundle\Form\TeamsType;
use Ffjv\BoBundle\Form\CreateUserType;
use Ffjv\BoBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class ClubsController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(){
        $clubs = $this->get('clubs')->findAll();

        return $this->render('FfjvFoBundle:Clubs:index.html.twig', array(
            'clubs' => $clubs
        ));
    }

    /**
     * @param int $clubId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($clubId = 0)
    {
        $club = $this->getDoctrine()->getRepository('FfjvBoBundle:Clubs')->findOneBy(array('id' => $clubId));
        $members = $this->getDoctrine()->getRepository('FfjvBoBundle:UserHasClubs')->findBy(array('club' => $club, 'requestToJoin' => 0));
        $userRequestTojoin = $this->getDoctrine()->getRepository('FfjvBoBundle:UserHasClubs')->getRequestUserToJoin($club);
        $countMembersActive = $this->get('clubs')->getCountMemberActive($club->getId());

        $contactForm = $this->getContactForm($this->generateUrl('fo_clubs_contact'), array(
            'user' => $this->getUser()->getId(),
            'club' => $club->getId()
        ));

        $joinContactForm = $this->get('user_has_clubs')->getJoinClubForm($this->generateUrl('fo_request_user_has_club'), array(
            'user' => $this->get('security.token_storage')->getToken()->getUser()->getId(),
            'club' => $club->getId()
        ));

        return $this->render('@FfjvFo/Clubs/show.html.twig', array(
            'club'      => $club,
            'members'    => $members,
            'requestMembers'    => $userRequestTojoin,
            'user_is_member' => $this->isMember($club),
            'count_members' => $countMembersActive,
            'contact_form'  => $contactForm->createView(),
            'join_club_form' => $joinContactForm->createView()
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $club = new Clubs();
        $form = $this->getClubForm($club);

        return $this->render('@FfjvFo/Clubs/new.html.twig', array(
            'form' => $form->createView(),
            'user' => $this->getUser()
        ));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $club = new Clubs();
        $form = $this->getClubForm($club);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $club->setUser($this->getUser());
            $club->setIdZipCode($this->get('clubs')->getIdZipCode($club->getZipCode(), $club->getCountry()));
            $em->persist($club);
            $em->flush();

            $licence = $this->get('licences_clubs')->getNewLicences($club);
            $club->setLicence($licence);
            $em->persist($club);
            $em->flush();

            $this->addMemberToClub($club, $this->getUser(), array('ROLE_AUTHOR'));


            $this->addFlash('success', 'Félicitaion votre club a été créé');

            return $this->redirect($this->generateUrl('fo_clubs_show', array('clubId' => $club->getId())));
        }
        $this->addFlash('error', 'une erreur c\'est produite');

        return $this->render('FfjvFoBundle:Clubs:new.html.twig', array(
            'form' => $form->createView(),
            'user' => $this->getUser()
        ));
    }

    /**
     * @param string $clubId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($clubId = '')
    {
        $em   = $this->getDoctrine()->getManager();
        $club = $em->getRepository('FfjvBoBundle:Clubs')->findOneBy(array('id' => $clubId));
        //if user is author
        if ($club->getUser() == $this->getUser()) {
            $path = $this->generateUrl('fo_clubs_update', array('clubId' => $clubId));
            $form = $this->getClubForm($club, $path);

            return $this->render('FfjvFoBundle:Clubs:edit.html.twig', array(
                'edit_form' => $form->createView(),
                'club' => $club
            ));
        }

        return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
    }

    /**
     * @param Request $request
     * @param string  $clubId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $clubId = '')
    {
        $em   = $this->getDoctrine()->getManager();
        $club = $em->getRepository('FfjvBoBundle:Clubs')->findOneBy(array('id' => $clubId));
        //if club not esixt || user is not author
        if (!$club || $club->getUser() != $this->getUser()) {
            $this->addFlash('error', 'une erreur c\'est produite');

            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }
        $path = $this->generateUrl('fo_clubs_update', array('clubId' => $clubId));
        $form = $this->getClubForm($club, $path);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $club->setUser($this->getUser());
            $club->setIdZipCode($this->get('clubs')->getIdZipCode($club->getZipCode(), $club->getCountry()));
            $em->persist($club);
            $em->flush();
            // TODO AJOUTER L'ENVOIE DE MAIL DE CONFIRMATION
            $this->addFlash('success', 'Félicitaion votre club a été édité');

            return $this->redirectToRoute('fo_clubs_show', array('clubId' => $clubId));
        }
        $this->addFlash('error', 'une erreur c\'est produite');

        return $this->render('FfjvFoBundle:Clubs:edit.html.twig', array(
            'edit_form' => $form->createView(),
            'club' => $club
        ));
    }

    /**
     * @param string $clubId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction($clubId = '')
    {
        $em   = $this->getDoctrine()->getManager();
        $club = $em->getRepository('FfjvBoBundle:Clubs')->findOneBy(array('id' => $clubId));
        //if club not esixt || user is not author
        if (!$club || $club->getUser() != $this->getUser()) {
            $this->addFlash('error', 'une erreur c\'est produite');

            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }
        $form = $this->getDeleteClubForm($clubId);

        return $this->render('@FfjvFo/Clubs/delete.html.twig', array(
            'deleteForm' => $form->createView(),
            'club' => $club
        ));
    }

    /**
     * @param Request $request
     * @param string  $clubId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $clubId = '')
    {
        $form = $this->getDeleteClubForm($clubId);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em   = $this->getDoctrine()->getManager();
            $club = $em->getRepository('FfjvBoBundle:Clubs')->findOneBy(array('id' => $clubId));
            //if club not esixt || user is not author
            if (!$club || $club->getUser() != $this->getUser()) {
                $this->addFlash('error', 'une erreur c\'est produite');

                return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
            }
            $em->remove($club);
            $em->flush();
            $this->addFlash('success', 'votre club a bien été supprimer');

            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }
        $this->addFlash('error', 'une erreur c\'est produite');

        return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));


    }

    /**
     * @param string $clubId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newTeamAction($clubId){
        $em = $this->getDoctrine()->getManager();
        $club = $em->getRepository('FfjvBoBundle:Clubs')->find($clubId);
        //if club not esixt || user is not author
        if (!$club || $club->getUser() != $this->getUser()) {
            $this->addFlash('error', 'une erreur c\'est produite');

            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }
        $team = new Teams();

        $url = $this->generateUrl('fo_clubs_createteams', array('clubId' => $club->getId()));
        $form = $this->getTeamsForm($team, $url);

        return $this->render('@FfjvFo/Clubs/newTeams.html.twig', array(
            'club' => $club,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param string  $clubId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createTeamToClubAction(Request $request, $clubId = ''){

        $em = $this->getDoctrine()->getManager();
        $club = $em->getRepository('FfjvBoBundle:Clubs')->findOneBy(array('id' => $clubId));
        //if club not esixt || user is not author
        if (!$club || $club->getUser() != $this->getUser()) {
            $this->addFlash('error', 'une erreur c\'est produite');

            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }

        $team = new Teams();
        $url = $this->generateUrl('fo_clubs_createteams', array('clubId' => $clubId));
        $form = $this->getTeamsForm($team, $url);
        $form->handleRequest($request);
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $licence = $this->get('licences_teams')->getNewLicences($club);
            $team->setClub($club);
            $team->setUser($this->getUser());
            $team->setLicence($licence);
            $em->persist($team);
            $em->flush();
            $club->addTeam($team);
            $em->persist($club);
            $em->flush();
            $this->addFlash('success', 'Votre nouvelle team a été créée .');
            return $this->redirectToRoute('fo_clubs_show', array('clubId' => $clubId));
        }
        $this->addFlash('error', 'une erreur c\'est produite');
        return $this->redirectToRoute('fo_clubs_show', array('clubId' => $clubId));
    }

    /**
     * @param string $clubId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newMemberAction($clubId = ''){
        $em = $this->getDoctrine()->getManager();
        $club = $em->getRepository('FfjvBoBundle:Clubs')->findOneBy(array('id' => $clubId));
        //if club not esixt || user is not author
        if (!$club || $club->getUser() != $this->getUser()) {
            $this->addFlash('error', 'une erreur c\'est produite');

            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }
        $user = new User();

        $url = $this->generateUrl('fo_clubs_createmembers', array('clubId' => $clubId));
        $form = $this->getUserForm($user, $url);

        return $this->render('@FfjvFo/Clubs/newMembers.html.twig', array(
            'club' => $club,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param string  $clubId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createMemberToClubAction(Request $request, $clubId = ''){

        $em = $this->getDoctrine()->getManager();
        $club = $em->getRepository('FfjvBoBundle:Clubs')->findOneBy(array('id' => $clubId));

        //if club not esixt || user is not author
        if (!$club || $club->getUser() != $this->getUser()) {
            $this->addFlash('error', 'une erreur c\'est produite');
            return $this->redirectToRoute('fo_profile_show', array('userUsername' => $this->getUser()->getUsername()));
        }

        $user = new User();
        $url = $this->generateUrl('fo_clubs_createmembers', array('clubId' => $clubId));
        $form = $this->getUserForm($user, $url);
        $form->handleRequest($request);
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $pass = str_pad(base_convert(md5(uniqid(mt_rand(), true)), 16, 36), 0, 10);
            //set password
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $pass);
            $user->setPassword($encoded);
            $user->setAuthorUser($this->getUser());
            $user->setCgu(false);
            //set zipcode
            $user->setIdZipCode($this->get('user')->getIdZipCode($user->getZipCode(), $user->getNationality()));
            //persiste and flush
            $em->persist($user);
            $em->flush();
            //add user has club
            $this->addMemberToClub($club, $user);

            $this->addFlash('success', 'Votre nouveau membre a été créé .');
            return $this->redirectToRoute('fo_clubs_show', array('clubId' => $clubId));
        }
        $this->addFlash('error', 'une erreur c\'est produite');
        return $this->render('@FfjvFo/Clubs/newMembers.html.twig', array(
            'club' => $club,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function contactClubAction(Request $request){

        $form = $this->getContactForm();
        $form->handleRequest($request);

        if($form->isValid()){
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $id = $data['club'];
            $club = $em->getRepository('FfjvBoBundle:Clubs')->find($id);
            if (!$club) {
                throw $this->createNotFoundException('Unable to find Clubs club.');
            }
            // create and save Mesage
            $message = new Messages();
            $message->setAuthorUser($this->getUser());
            $message->setMessage($data['content']);
            $message->setSubject($data['subject']);
            $message->setClub($club);
            $message->setEmail($club->getEmail());
            $em->persist($message);
            $em->flush();
            //send message
            if($this->get('contact')->contactClub($message)){
                $this->addFlash('success', 'Votre message a bien été envoyer');
            } else {
                $this->addFlash('error', 'Une erreur c\'est produite ! Votre message n\'a pus être envoyé .');
            }
            return $this->redirectToRoute('fo_clubs_show', array('clubId' => $club->getId()));
        }
        $this->addFlash('error', 'Une erreur c\'est produite');
        return $this->redirectToRoute('fo_profile_show', array('id' => $this->getUser()->getId()));
    }


    /* ****** PRIVATE ***************************************** */

    /**
     * @param Clubs $club
     * @param User $user
     * @param array $role
     * @return bool
     */
    private function addMemberToClub(Clubs $club, User $user, $role = array("ROLE_USER")){
        $em = $this->getDoctrine()->getManager();

        $userHasClub = new UserHasClubs();
        $userHasClub->setClub($club);
        $userHasClub->setUser($user);
        $userHasClub->setRoles($role);

        $em->persist($userHasClub);

        $em->flush();

        return true;
    }

    /**
     * @param User $user
     * @param string $url
     * @return \Symfony\Component\Form\Form
     */
    private function getUserForm(User $user, $url = ''){
        $registerForm = $this->createForm(new CreateUserType(true, true), $user, array(
            'action' => $url,
            'method' => 'POST'
        ));
        $registerForm->add('submit', 'submit', array(
            'label' => 'enregistrer'
        ));
        return $registerForm;
    }


    /**
     * @param Teams  $teams
     * @param string $url
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getTeamsForm(Teams $teams, $url = ''){
        $form = $this->createForm(new TeamsType(), $teams, array(
            'method' => 'POST',
            'action' => $url
        ));
        $form->add('submit', 'submit', array(
            'label' => 'créer'
        ));
        return $form;
    }

    /**
     * @param Clubs  $club
     * @param string $path
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getClubForm(Clubs $club, $path = '')
    {
        if ($path == '') {
            $path = $this->generateUrl('fo_clubs_create');
        }
        $form = $this->createForm(new ClubsType(), $club, array(
            'method' => 'POST',
            'action' => $path
        ));
        $form->add('submit', 'submit', array(
            'label' => 'Enregistrer',
            'attr'  => array('class' => 'btn btn-success')
        ));

        return $form;
    }

    /**
     * @param string $clubId
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getDeleteClubForm($clubId = '')
    {
        $form = $this->createFormBuilder();
        $form->add('title', 'hidden', array(
            'attr' => array('value' => $clubId)
        ));
        $form->add('submit', 'submit', array(
            'label' => 'supprimer',
            'attr'  => array('class' => 'btn btn-danger')
        ));
        $form->setAction($this->generateUrl('fo_clubs_delete', array('clubId' => $clubId)));
        $form->setMethod('POST');

        return $form->getForm();
    }

    /**
     * @param string $url
     * @param array $data
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function getContactForm($url = '', $data = array()){
        $form = $this->get('contact')->getFormContactClub($url, $data);
        $form->add('submit', 'submit', array(
            'label' => 'envoyer'
        ));
        return $form;
    }

    /**
     * @param Clubs $club
     *
     * @return bool
     */
    private function isMember(Clubs $club){

        $em = $this->getDoctrine()->getRepository('FfjvBoBundle:UserHasClubs')
            ->findOneBy(array('user' => $this->get('security.token_storage')->getToken()->getUser(), 'club' => $club));


        if($em !== null){
            return true;
        };


        return false;
    }


}
