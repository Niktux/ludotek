<?php

namespace Ludo\Models;

class Game
{
    const
        API_DOMAIN = 'http://api.ludo.deboo.fr',
        FRONT_DOMAIN = 'http://nico.ludotheque.net';
    
    private
        $id,
        $name,
        $originalName,
        $editeurId,
        $editeurName,
        $duration,
        $minPlayer,
        $maxPlayer,
        $thumbnailPicture,
        $gamePicture,
        $boxPicture,
        $minAge,
        $db;
    
    public static function get(\Doctrine\DBAL\Driver\Connection $db, $id)
    {
        // FIXME prototype without tests
        $game = $db->fetchAssoc(
            'SELECT j.*, e.nom AS editeur
            FROM ludo_jeu AS j
            INNER JOIN ludo_editeur AS e USING (idediteur)
            WHERE idjeu = ?',
            array((int) $id)
        );
        
        $class = __CLASS__;
        return new $class($db, $game);
    }
    
    public function __construct(\Doctrine\DBAL\Driver\Connection $db, array $row)
    {
        $this->db = $db;
        $this->hydrate($row);
    }
    
    private function hydrate($row)
    {
        $this->id           = $row['idjeu'];
        $this->name         = $row['jeu'];
        $this->originalName = $row['nom_vo'];
        $this->editeurId    = $row['idediteur'];
        $this->editeurName  = $row['editeur'];
        $this->duration     = $row['duree'];
        $this->minPlayer    = $row['jmin'];
        $this->maxPlayer    = $row['jmax'];
        $this->minAge       = $row['age'];
        
        $this->thumbnailPicture = $this->fixPicture($row['img_miniature']);
        $this->boxPicture       = $this->fixPicture($row['img_boite']);
        $this->gamePicture      = $this->fixPicture($row['img_jeu']);
    }
    
    private function fixPicture($url)
    {
        if(stripos($url, './img_copied') !== false)
        {
            $url = self::FRONT_DOMAIN . substr($url, 1);
        }
        
        return $url;
    }
    
    public function toArray()
    {
        $self = $this->getSelf();
        
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'originalName' => $this->originalName,
            'editeur' => array(
                'id' => $this->editeurId,
                'name' => $this->editeurName,
            ),
            'duration' => $this->duration,
            'minAge' => $this->minAge,
            'configuration' => array(
                'min' => $this->minPlayer,
                'max' => $this->maxPlayer,
            ),
            'pictures' => array(
                'thumbnail' => $this->thumbnailPicture,
                'box' => $this->boxPicture,
                'game' => $this->gamePicture,
            ),
            '_links' => array(
                'self' => $self,
                'href' => sprintf(self::FRONT_DOMAIN . '/viewgame.php?idj=%d', $this->id),
                'authors' => $self . '/authors',
            )
        );
    }
    
    private function getSelf()
    {
        // TODO reuse routing
        return sprintf(self::API_DOMAIN . '/games/' . $this->id);
    }
    
    public function getAuthors()
    {
        $statement = $this->db->executeQuery(
           'SELECT *
            FROM ludo_auteur_jeu AS aj
            INNER JOIN ludo_auteur AS a USING (idauteur)
            WHERE aj.idjeu = ?',
            array((int) $this->id)
        );
        
        $authors = array();
        foreach($statement as $authorRow)
        {
            $author = new Author($this->db, $authorRow);
            $authors[] = $author->toArray();
        }
        
        return $authors;
    }
}