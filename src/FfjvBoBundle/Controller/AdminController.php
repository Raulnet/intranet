<?php

namespace FfjvBoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {

        $clubs = $this->get('clubs')->getLastCreated(5);
        $users = $this->get('user')->getLastRegister(5);
        $messages = $this->get('messages')->getLastSend(5);
        $teams = $this->get('teams')->getLastRegistered(5);

        return $this->render('@FfjvBo/Admin/index.html.twig', array(
            'clubs'             => $clubs,
            'users'             => $users,
            'messages'          => $messages,
            'teams'             => $teams
        ));
    }
}
