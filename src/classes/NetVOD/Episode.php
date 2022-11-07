<?php

class Episode
{
    protected int $numero;
    protected string $titre;
    protected int $duree;
    protected string $resume;
    protected string $cheminImage;
    protected string $cheminFich;

    /**
     * @param int $numero
     * @param string $titre
     * @param int $duree
     * @param string $resume
     * @param string $cheminImage
     * @param string $cheminFich
     */
    public function __construct(int $numero, string $titre, int $duree, string $resume, string $cheminImage, string $cheminFich)
    {
        $this->numero = $numero;
        $this->titre = $titre;
        $this->duree = $duree;
        $this->resume = $resume;
        $this->cheminImage = $cheminImage;
        $this->cheminFich = $cheminFich;
    }


    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
}