<?php

namespace FfjvFoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ToolsController extends Controller
{
    const ROOT_RESOURCE_FOLDER = '@FfjvFoBundle/Resources/translations/';
    const GLOBAL_FILENAME = 'messages.fr.json';

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
        $globalFilename = $kernel->locateResource(self::ROOT_RESOURCE_FOLDER.self::GLOBAL_FILENAME);
        if(!file_exists($globalFilename)){
            throw new \Exception('This file '. self::GLOBAL_FILENAME .' doesn\'t exist');
        }

        $globalContent = file_get_contents($globalFilename);
        $content = file_get_contents($filename);
        $newFile = "***** Traduction de la page"."\n".$content."\r"." "."\n"." "."\n"."***** Traduction Global"."\n".$globalContent;
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$fileNameDownloaded.'.fr.txt');
        $response->setContent($newFile);
        $response->send();
        return $response;
    }
}
