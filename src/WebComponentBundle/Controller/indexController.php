<?php

namespace WebComponentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class indexController extends Controller
{
    public function indexAction()
    {
        $listEvent = $this->get('weezeventapi')->getListEvent();
        return $this->render('WebComponentBundle:Index:index.html.twig', array(
            'events' => $listEvent 
        ));
    }
}
