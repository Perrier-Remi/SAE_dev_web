<?php

namespace iutnc\netvod\NetVOD;

use iutnc\netvod\bd\ConnectionFactory;

class Serie
{
    protected string $titre;
    protected string $genre;
    protected string $public;
    protected string $descriptif;
    protected int $dateSortie;
    protected string $dateAjout;
    protected string $cheminImage;
    protected string $id;

    /**
     * @param string $titre
     * @param string $genre
     * @param string $public
     * @param string $descriptif
     * @param int $dateSortie
     * @param string $dateAjout
     */
    public function __construct(string $titre, string $cheminImage, string $descriptif, int $dateSortie, string $dateAjout,string $id_serie,string $genre='action',$public='adulte')
    {
        $this->titre = $titre;
        //$this->cheminImage=$cheminImage;
        $this->cheminImage='video/thumbnail.png';
        $this->descriptif = $descriptif;
        $this->dateSortie = $dateSortie;
        $this->dateAjout = $dateAjout;
        $this->id = $id_serie;
        $this->public=$public;
        $this->genre=$genre;

    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public static function getNoteMoyenne(int $id):float{
        $db = ConnectionFactory::makeConnection();

        $query2 = "SELECT note FROM commentaires WHERE id_serie=?";
        $result2 = $db->prepare($query2);
        $result2->execute([$id]);
        $result2->setFetchMode(\PDO::FETCH_ASSOC);
        $retour=0;
        $nbNotes=0;
        while($data=$result2->fetch()){
            $retour += $data['note'];
            $nbNotes++;
        }
        if ($nbNotes==0) $nbNotes=1;
        $retour = round($retour/$nbNotes,1);
        $query2 = "UPDATE serie SET noteMoyenne = ? WHERE id=?";
        $result2 = $db->prepare($query2);
        $result2->execute([$retour,$id]);
        return $retour ;
    }
}