<?php

namespace WebComponentBundle\Controller;

use FfjvBoBundle\Entity\WeezeventApiLog;
use FfjvBoBundle\Form\WeezEventApiLogType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class indexController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
//
//        $wcService = $this->get('web_component');
//
//        $response = $wcService->cacheComponentMinify();
        
        return $this->render('WebComponentBundle:Index:index.html.twig', array(
            
        ));
    }

    /**
     * @param Request $request
     */
    public function getTabContent(Request $request){
        
    }
    
}
