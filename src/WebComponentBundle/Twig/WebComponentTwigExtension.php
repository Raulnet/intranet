<?php
/**
 * Created by PhpStorm.
 * User: raulnet
 * Date: 27/05/16
 * Time: 21:43
 */

namespace WebComponentBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class WebComponentTwigExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface|null
     */
    protected $container = null;

    /**
     * AppExtension constructor.
     * @param ContainerInterface|null $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getFunctions(){

        return [
            new \Twig_SimpleFunction('webComponent', [$this, 'webComponent'], []),
        ];
    }

    /**
     * @param array $webComponent
     * @param bool $liteDependencie
     * @return string
     */
    public function webComponent(array $webComponent, $liteDependencie = true)
    {
        return $this->container->get('web_component')->getAllLink($webComponent, $liteDependencie);
    }

    /**
     * @return string
     */
    public function getName(){
        return 'web_component_twig_extension';
    }
    
}
