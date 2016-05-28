<?php
/**
 * Created by PhpStorm.
 * User: raulnet
 * Date: 27/05/16
 * Time: 21:45
 */

namespace WebComponentBundle\Service;

use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WebComponentService
{
    const COMPONENT_EXTEND = '.html';
    const WEB_COMPONENT_LITE = 'webcomponentsjs/webcomponents-lite.min.js';
    const WEB_COMPONENT = 'webcomponentsjs/webcomponents.min.js';

    /**
     * @var string
     */
    private $pathLib = "";

    /**
     * @var string
     */
    private $pathComponents = "";

    /**
     * @var array
     */
    private $componentArray = [];

    /**
     * @var bool
     */
    private $webComponents = self::WEB_COMPONENT_LITE;

    /**
     * WebComponentService constructor.
     * @param ContainerInterface $container
     * @param Packages $packages
     * @param string $pathLib
     * @param string $pathComponents
     * @param array $componentArray
     */
    public function __construct(ContainerInterface $container, Packages $packages, $pathLib = "", $pathComponents = "",array $componentArray)
    {
        $request = $container->get('request_stack');
        $request = $request->getCurrentRequest();
        $scheme = $request->getScheme();
        $httpHost = $request->getHttpHost();
        $asset = $packages->getUrl($pathComponents);
        $this->pathLib = $packages->getUrl($pathLib);
        $this->pathComponents =  $scheme.'://'.$httpHost.$asset;
        $this->componentArray = $componentArray;
    }

    /**
     * @param array $components
     * @param bool $liteDependencie
     * @return string
     */
    public function getAllLink(array $components, $liteDependencie = true){
        if(!$liteDependencie){
            $this->webComponents = self::WEB_COMPONENT;
        }
        $links = $this->getDependencies($components);
        $dom = '<script src="'.$this->pathLib.$this->webComponents.'"></script>'."\n";
        foreach($links as $link){
            $dom .= $link."\n";        }
        return $dom;
    }

    /**
     * @param array $components
     * @return array
     */
    private function getDependencies(array $components){

        $dependencies = [];
        foreach($components as $component){

            $componentDepencies = $this->componentArray[$component];
            foreach($componentDepencies as $depency){
                $dependencies[$depency] = $this->getLinkImportComponent($this->pathLib, $depency);
            }
            $dependencies[$component] = $this->getLinkImportComponent($this->pathComponents, $component.self::COMPONENT_EXTEND);
        }
        return $dependencies;
    }

    /**
     * @param string $path
     * @param string $component
     * @return string
     */
    private function getLinkImportComponent($path = "", $component = ""){

        return '<link rel="import" href="'.$path.$component.'">';
    }
}