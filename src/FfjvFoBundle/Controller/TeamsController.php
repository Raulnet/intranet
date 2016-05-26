<?php

namespace FfjvFoBundle\Controller;

use FfjvBoBundle\Entity\Teams;
use FfjvBoBundle\Form\TeamsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use FfjvBoBundle\Entity\Messages;

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
        $teamMembers = $em->getRepository('FfjvBoBundle:UserHasTeams')->findBy(['team' => $team]);
        $editForm = $this->getFormTeamEdit($team);
        $deleteForm = $this->getDeleteForm($team);
        $contactForm = $this->getContactForm($this->generateUrl('fo_teams_clubs_contact', ['teamId' => $teamId]), array(
            'user' => $this->getUser()->getId(),
            'club' => $team->getClub()->getId()
        ));
        return $this->render('FfjvFoBundle:Teams:show.html.twig', [
            'team'          => $team,
            'editForm'      => $editForm->createView(),
            'deleteForm'    => $deleteForm->createView(),
            'contact_form'  => $contactForm->createView(),
            'members'       => $teamMembers
        ]);
    }

    /**
     * @param Request $request
     * @param int $teamId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     * @throws \Exception
     */
    public function updateAction(Request $request, $teamId = 0){
        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('FfjvBoBundle:Teams')->find($teamId);
        $authorizationChecker = $this->get('security.authorization_checker');

        if (false === $authorizationChecker->isGranted('EDIT', $team->getClub())) {
            throw new AccessDeniedException();
        }
        if($this->get('security'))
        if(!$team){
            throw new \Exception('this id team not exist');
        }
        $editForm = $this->getFormTeamEdit($team);
        $editForm->handleRequest($request);
        $deleteForm = $this->getDeleteForm($team);
        if($editForm->isValid()){
            $em->persist($team);
            $em->flush();
            $this->addFlash('success', 'Votre Team a été mise a jours !');
        } else {
            $this->addFlash('error', 'une erreur c\'est produite . Votre team n\'a pus être mise à jour.');
        }
        return $this->render('FfjvFoBundle:Teams:show.html.twig', [
            'team'          => $team,
            'editForm'      => $editForm->createView(),
            'deleteForm'    => $deleteForm->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param int $teamId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws AccessDeniedException
     * @throws \Exception
     */
    public function deleteAction(Request $request, $teamId = 0){
        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('FfjvBoBundle:Teams')->find($teamId);

        $authorizationChecker = $this->get('security.authorization_checker');
        if (false === $authorizationChecker->isGranted('DELETE', $team->getClub())) {
            throw new AccessDeniedException();
        }
        if(!$team){
            throw new \Exception('this id team not exist');
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
     * @param Request $request
     * @param int $teamId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function contactClubTeamAction(Request $request, $teamId = 0){

        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('FfjvBoBundle:Teams')->find($teamId);
        if(!$team){
            throw new \Exception('this id team not exist');
        }

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
            return $this->redirectToRoute('fo_teams_show', array('teamId' => $team->getId()));
        }
        $this->addFlash('error', 'Une erreur c\'est produite');
        return $this->redirectToRoute('fo_profile_show', array('id' => $this->getUser()->getId()));
    }

    /** ********* PRIVATE ********** */

    /**
     * @param Teams $team
     * @return $this|\Symfony\Component\Form\FormInterface
     */
    private function getFormTeamEdit(Teams $team){
        return $this->createForm(TeamsType::class, $team, [
            "method" => "PUT",
            "action" => $this->generateUrl("fo_teams_update", ["teamId" => $team->getId()])
        ])->add("submit", SubmitType::class, [
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
            ->add('submit', SubmitType::class, [
                'label' => 'Confirmer',
                'attr'  => ["class" => "btn btn-danger"]
            ])
            ->getForm();

    }

    /**
     * @param string $url
     * @param array $data
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function getContactForm($url = '', $data = array()){
        $form = $this->get('contact')->getFormContactClub($url, $data);
        $form->add('submit', SubmitType::class, array(
            'label' => 'envoyer'
        ));
        return $form;
    }
}
