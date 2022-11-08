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

    /**
     * @param string $titre
     * @param string $genre
     * @param string $public
     * @param string $descriptif
     * @param int $dateSortie
     * @param string $dateAjout
     */
    public function __construct(string $titre, string $genre='action', string $public, string $descriptif, int $dateSortie, string $dateAjout)
    {
        $this->titre = $titre;
        $this->genre = $genre;
        $this->public = $public;
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