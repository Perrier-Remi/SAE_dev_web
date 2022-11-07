<?php

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
     * @param array $listeEpisode
     */
    public function __construct(string $titre, string $genre, string $public, string $descriptif, int $dateSortie, string $dateAjout, array $listeEpisode)
    {
        $this->titre = $titre;
        $this->genre = $genre;
        $this->public = $public;
        $this->descriptif = $descriptif;
        $this->dateSortie = $dateSortie;
        $this->dateAjout = $dateAjout;
        $this->listeEpisode = $listeEpisode;
    }


    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

}