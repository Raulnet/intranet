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
use JSqueeze;

class WebComponentService
{
    const COMPONENT_EXTEND = '.html';
    const WEB_COMPONENT_MINIFY_EXTEND = '.wcm';
    const WEB_COMPONENT_LITE = 'webcomponentsjs/webcomponents-lite.min.js';
    const WEB_COMPONENT = 'webcomponentsjs/webcomponents.min.js';
    const REGEX_HTML_SCRIPT = '#<script>(.*)</script>#isU';
    const REGEX_HTML_COMMENT = '#<!--(.*)-->#isU';
    const INDEX_TAG_SCRIPT = '%%Script';
    const CACHE_COMPONENT_MINIFY_FOLDER = '/webComponentMinify/';

    /**
     * @var string
     */
    private $pathLib = "";

    /**
     * @var string
     */
    private $pathComponents = "";

    /**
     * @var string
     */
    private $folderComponent = "";

    /**
     * @var array
     */
    private $componentArray = [];

    /**
     * @var bool
     */
    private $webComponents = self::WEB_COMPONENT_LITE;

    /**
     * @var string
     */
    private $kernelCacheDir = '';

    /**
     * @var string
     */
    private $kernelRootDir = '';

    /**
     * WebComponentService constructor.
     *
     * @param ContainerInterface $container
     * @param Packages           $packages
     * @param string             $pathLib
     * @param string             $pathComponents
     * @param array              $componentArray
     */
    public function __construct(ContainerInterface $container, Packages $packages, $pathLib = "", $pathComponents = "",array $componentArray)
    {
        $kernel = $container->get('kernel');
        $this->kernelCacheDir = $kernel->getCacheDir();
        $this->kernelRootDir = $kernel->getRootDir();
        $this->folderComponent = $pathComponents;

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
     * @param bool $removeHtmlComment
     *
     * @return bool
     * @throws \Exception
     */
    public function cacheComponentMinify($removeHtmlComment = true){
            $componentFilename = $this->kernelRootDir.'/../web/component/webcomponent_index_index-element.html';
            $this->minifyComponent($componentFilename, '', $removeHtmlComment);
        return true;
    }

    /**
     * @param string $filename
     * @param string $component
     * @param bool   $removeHtmlComment
     *
     * @return int
     * @throws \Exception
     */
    private function minifyComponent($filename = '', $component = '', $removeHtmlComment = true){

        if(!file_exists($filename)){
            throw new \Exception('File '.$filename.' not exist');
        }
        $file = file_get_contents($filename);
        // remove html comment
        if($removeHtmlComment){
            $file = preg_replace(self::REGEX_HTML_COMMENT, '', $file);
        }
        // remove indantation
        $file = preg_replace(['/\s{4,}/', '/[\t\n]/'] , '', $file);
        $scriptContainer = [];
        $jz = new JSqueeze();
        //catch all script JS
//        do{
//            $response = preg_match(self::REGEX_HTML_SCRIPT, $file, $m);
//            if($response === 1){
//                $tag = self::INDEX_TAG_SCRIPT.count($scriptContainer);
//                $minifiedJs = $jz->squeeze(
//                    $m[0],
//                    true,   // $singleLine
//                    false,  // $removeImportantComments
//                    false   // $specialVarRx
//                );
//                $scriptContainer[$tag] = $minifiedJs;
//                $file = str_replace($m[0], $tag, $file);
//            }
//        }while($response  == 1);
//        foreach ($scriptContainer as $tag => $miniScript){
//            $file = str_replace($tag, $miniScript, $file);
//        }

        $dir = $this->kernelCacheDir.self::CACHE_COMPONENT_MINIFY_FOLDER;
        $dir = $dir ?: sys_get_temp_dir();

        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            throw new \RuntimeException("Unable to create cache directory: $dir");
        }

        $cacheFilename = $dir.$component.self::WEB_COMPONENT_MINIFY_EXTEND;
        return file_put_contents($filename, $file);
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