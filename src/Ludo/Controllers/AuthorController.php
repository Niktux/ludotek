<?php

namespace Ludo\Controllers;

use Ludo\Models\Author;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthorController
{
    private
        $db;
    
    public function __construct(\Doctrine\DBAL\Driver\Connection $db)
    {
        $this->db = $db;
    }
    
    public function authorAction($authorId)
    {
        $author = Author::get($this->db, $authorId);
        
        return new JsonResponse($author->toArray());
    }
}