<?php

namespace Ludo\Models;

class Author
{
    private
        $id,
        $name,
        $url,
        $image,
        $db;
    
    public static function get(\Doctrine\DBAL\Driver\Connection $db, $id)
    {
        // FIXME prototype without tests
        $author = $db->fetchAssoc(
            'SELECT * FROM ludo_auteur WHERE idauteur = ?',
            array((int) $id)
        );
        
        $class = __CLASS__;
        return new $class($db, $author);
    }
    
    public function __construct(\Doctrine\DBAL\Driver\Connection $db, array $row)
    {
        $this->db = $db;
        $this->hydrate($row);
    }
    
    private function hydrate($row)
    {
        $this->id    = $row['idauteur'];
        $this->name  = $row['nom'];
        $this->url   = $row['url'];
        $this->image = $row['image'];
    }
    
    public function toArray()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'image' => $this->image,
        );
    }
}
