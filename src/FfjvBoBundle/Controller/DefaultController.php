<?php

namespace FfjvBoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FfjvBoBundle:Default:index.html.twig', array());
    }
}
