<?php

namespace WebComponentBundle\Controller;

use FfjvBoBundle\Entity\WeezeventApiLog;
use FfjvBoBundle\Form\WeezEventApiLogType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class indexController extends Controller
{
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $apiLog = $em->getRepository('FfjvBoBundle:WeezeventApiLog')->findOneBy(['user' => $this->getUser()]);
        if(!$apiLog){
            $apiLog = new WeezeventApiLog();
            $listEvent = json_encode([]);
        } else {
            $this->get('weezeventapi')->setAuthAccess($apiLog->getApiUsername(), $apiLog->getApiPassword(), $apiLog->getApiKey());
            $listEvent = $this->get('weezeventapi')->getListEvent();
        }

        
        $form = $this->createForm(WeezEventApiLogType::class, $apiLog, ['method' => 'POST']);
        if($request->getMethod() == 'POST'){
            $form->handleRequest($request);
            if($form->isSubmitted()){
                $apiLog->setUser($this->getUser());
                $em->persist($apiLog);
                $em->flush();
            }
        }

        return $this->render('WebComponentBundle:Index:index.html.twig', array(
            'events' => $listEvent,
            'form'  => $form->createView()
        ));
    }
}
