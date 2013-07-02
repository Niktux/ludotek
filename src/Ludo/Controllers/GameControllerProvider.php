<?php

namespace Ludo\Controllers;

use Silex\Application;
use Silex\ControllerProviderInterface;

class GameControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $app['game.controller'] = $app->share(function() use($app) {
            return new GameController($app['db']);
        });
        
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];
        
        $controllers->get('/{gameId}', 'game.controller:gameAction');
        $controllers->get('/{gameId}/authors', 'game.controller:authorsAction');
        
        return $controllers;
    }
}
