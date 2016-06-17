<?php

namespace FfjvFoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ToolsController extends Controller
{
    const ROOT_RESOURCE_FOLDER = '@FfjvFoBundle/Resources/translations/';

    /**
     * @param string $filename
     * @return Response
     * @throws AccessDeniedException
     * @throws \Exception
     */
    public function editTextAction($filename = "")
    {
        $authorizationChecker = $this->get('security.authorization_checker');
        if (false === $authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $kernel = $this->get('kernel');

        $fileNameDownloaded = $filename;

        $filename =$kernel->locateResource(self::ROOT_RESOURCE_FOLDER.$filename.'.fr.json');
        if(!file_exists($filename)){
            throw new \Exception('This file '. $filename .' doesn\'t exist');
        }
        $content = file_get_contents($filename);
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$fileNameDownloaded.'.fr.txt');
        $response->setContent($content);
        $response->send();
        return $response;
    }
}
