<?php

namespace Ludo\Controllers;

use Silex\Application;
use Silex\ControllerProviderInterface;

class AuthorControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $app['author.controller'] = $app->share(function() use($app) {
            return new AuthorController($app['db']);
        });
        
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];
        
        $controllers->get('/{authorId}', 'author.controller:authorAction');
        $controllers->post('/{authorId}', 'author.controller:createAuthorAction');
        
        return $controllers;
    }
}
