<?php

namespace Ludo;

use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

class Application extends \Silex\Application
{
    private
        $configuration;
    
    public function __construct($configurationFile)
    {
        parent::__construct();

        $this['startTime'] = microtime(true);
        $this['cache.path'] = __DIR__ . '/../../cache/';
        
        $this->loadConfiguration($configurationFile);
        $this->initializeDatabase();
        $this->initializeBuiltInServices();
        $this->initializeTemplateEngine();
    }
    
    private function loadConfiguration($configurationFile)
    {
        if(! is_file($configurationFile))
        {
            throw new \Exception("Configuration not found at location [$configurationFile]");
        }
        
        $this->configuration = Yaml::parse($configurationFile);
    }
    
    private function initializeDatabase()
    {
        if(! isset($this->configuration['db']['user'])
        || ! isset($this->configuration['db']['password']))
        {
            throw new \Exception('Missing database configuration (expecting db/user and db/password');
        }
        
        $this->register(new DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver'   => 'pdo_mysql',
                'host'     => 'localhost',
                'dbname'   => 'ludo',
                'user'     => $this->configuration['db']['user'],
                'password' => $this->configuration['db']['password'],
                'charset'  => 'utf8'
            ),
        ));
    }
    
    private function initializeBuiltInServices()
    {
        $this->register(new ServiceControllerServiceProvider());
        $this->register(new UrlGeneratorServiceProvider());
    }
    
    private function initializeTemplateEngine()
    {
        $this->register(new TwigServiceProvider(), array(
            'twig.path'    => array(__DIR__ . '/../../views'),
            'twig.options' => array('cache' => $this['cache.path'] . 'twig'),
        ));
    }
    
    public function enableDebug()
    {
        $this['debug'] = true;
        
        $this->register($p = new WebProfilerServiceProvider(), array(
            'profiler.cache_dir' => $this['cache.path'] . 'profiler',
        ));
        
        $this->mount('/_profiler', $p);
        
        return $this;
    }
    
    public function enableProfiling()
    {
        $startTime = $this['startTime'];
        
        $this->after(function (Request $request, Response $response) use($startTime){
            $response->headers->set('X-Generation-Time', microtime(true) - $startTime);
        });
        
        return $this;
    }
}