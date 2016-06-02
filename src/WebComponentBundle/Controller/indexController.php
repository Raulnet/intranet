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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if($request->getMethod() == 'POST'){
            $item = json_decode($request->getContent(), true);
            $content = $this->renderView('WebComponentBundle:Index/tabs:_tabs1.html.twig', [
               'item' => $item 
            ]);
         
            return new Response(json_encode(['content'=>$content, 'item'=> $item]), 200, ['Content-Type'=>'applcation/json']);
        }

        return $this->render('WebComponentBundle:Index:index.html.twig', array(
            
        ));
    }

    /**
     * @param Request $request
     */
    public function getTabContent(Request $request){
        
    }
}
