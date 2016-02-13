<?php
/**
 * Created by PhpStorm.
 * User: laurentnegre
 * Date: 16/01/2016
 * Time: 18:28
 */
namespace Ffjv\FoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ffjv\BoBundle\Entity\Messages;

class UserHasClubController extends Controller
{

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
            if($this->get('contact')->sendRequestToJoinCLub($message)){
                $this->addFlash('success', 'Votre message a bien été envoyée');
            } else {
                $this->addFlash('error', 'Une erreur c\'est produite ! Votre message n\'a pus être envoyé .');
            }
            return $this->redirectToRoute('fo_clubs_show', array('clubTitle' => $club->getTitle()));
        }

        exit('false');

        return $this->redirectToRoute('');

    }


}