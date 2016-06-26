<?php
/**
 * Created by PhpStorm.
 * User: laurentnegre
 * Date: 26/06/2016
 * Time: 19:59
 */
namespace AppBundle\Service;

use JmesPath;

class JmespathService
{
    const JMESPATH_CACHE_FOLDER = '/JmespathCache';

    /**
     * @var JmesPath\CompilerRuntime
     */
    private $runtime;

    /**
     * JmespathService constructor.
     *
     * @param string $kernelCacheDir
     */
    public function __construct($kernelCacheDir = "")
    {
        $this->runtime = new JmesPath\CompilerRuntime($kernelCacheDir.self::JMESPATH_CACHE_FOLDER);
    }


    /**
     * @param string $expression
     * @param array  $data
     *
     * @return mixed|null
     */
    public function search($expression = '', array $data){
        return $this->runtime->__invoke($expression, $data);
    }


}