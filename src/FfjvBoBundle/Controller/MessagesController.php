<?php

namespace FfjvBoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MessagesController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $messages = $this->get('messages')->findAll();

        return $this->render('FfjvBoBundle:Messages:index.html.twig', array(
            'messages' => $messages
        ));
    }

    /**
     * @param int $messageId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($messageId = 0)
    {

        $message = $this->getDoctrine()->getRepository('FfjvBoBundle:Messages')->find($messageId);

        return $this->render('FfjvBoBundle:Messages:show.html.twig', array(
            'message' => $message
        ));
    }
}
