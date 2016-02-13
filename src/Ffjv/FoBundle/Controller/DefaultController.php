<?php

namespace Ffjv\FoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FfjvFoBundle:Default:index.html.twig', array('name' => $name));
    }
}
