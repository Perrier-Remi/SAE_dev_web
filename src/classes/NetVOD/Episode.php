<?php
namespace iutnc\deefy\NetVOD;

namespace iutnc\netvod\NetVOD;

class Episode
{
    protected int $numero;
    protected string $titre;
    protected int $duree;
    protected string $cheminImage;

    /**
     * @param int $numero
     * @param string $titre
     * @param int $duree
     * @param string $cheminImage
     */
    public function __construct(int $numero, string $titre, int $duree, string $cheminImage)
    {
        $this->numero = $numero;
        $this->titre = $titre;
        $this->duree = $duree;
        $this->cheminImage = $cheminImage;
    }


    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
}