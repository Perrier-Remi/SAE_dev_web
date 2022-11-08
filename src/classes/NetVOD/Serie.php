<?php

namespace iutnc\netvod\NetVOD;

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
    public function __construct(string $titre, string $cheminImage, string $descriptif, int $dateSortie, string $dateAjout)
    {
        $this->titre = $titre;
        $this->cheminImage=$cheminImage;
        $this->descriptif = $descriptif;
        $this->dateSortie = $dateSortie;
        $this->dateAjout = $dateAjout;

    }


    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

}