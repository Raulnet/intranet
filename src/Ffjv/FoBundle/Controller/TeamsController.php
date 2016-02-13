<?php

namespace Ffjv\FoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TeamsController extends Controller
{
    public function newTeamsAction($name)
    {
        return $this->render('', array('name' => $name));
    }
}
