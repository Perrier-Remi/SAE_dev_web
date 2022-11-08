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
    protected array $listeEpisode;
    protected string $cheminImage;

    /**
     * @param string $titre
     * @param string $genre
     * @param string $public
     * @param string $descriptif
     * @param int $dateSortie
     * @param string $dateAjout
     */
    public function __construct(string $titre, string $cheminImage, string $descriptif, int $dateSortie, string $dateAjout,string $id_serie)
    {
        $this->titre = $titre;
        //$this->cheminImage=$cheminImage;
        $this->cheminImage='video/thumbnail.png';
        $this->descriptif = $descriptif;
        $this->dateSortie = $dateSortie;
        $this->dateAjout = $dateAjout;
        $this->generateListeEpisodes($id_serie);

    }
    public function generateListeEpisodes($id_serie){
        $db = ConnectionFactory::makeConnection();
        $query2 = "SELECT * FROM episode WHERE serie_id=?";
        $result2 = $db->prepare($query2);
        $result2->execute([$id_serie]);
        $res = $result2->fetch(\PDO::FETCH_ASSOC);
    }


    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

}