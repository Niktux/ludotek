<?php

namespace Ludo\Controllers;

use Ludo\Models\Game;

use Symfony\Component\HttpFoundation\JsonResponse;

class GameController
{
    private
        $db;
    
    public function __construct(\Doctrine\DBAL\Driver\Connection $db)
    {
        $this->db = $db;
    }
    
    public function gameAction($gameId)
    {
        $game = Game::get($this->db, $gameId);
        
        return new JsonResponse($game->toArray());
    }
    
    public function authorsAction($gameId)
    {
        $game = Game::get($this->db, $gameId);
        
        return new JsonResponse($game->getAuthors());
    }
}