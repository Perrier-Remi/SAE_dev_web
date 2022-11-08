<?php
namespace iutnc\netvod\render;

use iutnc\netvod\NetVOD\Serie;

class RenderSerie implements Renderer
{
    protected Serie $serie;
    public function __construct(Serie $serie)
    {
        $this->serie=$serie;
    }

    /**
     * @param $selector 0 pour compact, 1 pour complet
     * @return string
     */
    public function render($selector=0): string
    {
        $html = "";
        switch($selector){
            case 0:
                $html= $this->renderCompact();
                break;
            case 1:
                $html= $this->renderComplet();
                break;
        }
        return $html;
    }

    private function renderCompact():string
    {
        $html="<img src=".$this->serie->__get("cheminFichier")."   ".$this->serie->__get("titre");
        return $html;
    }
    private function renderComplet():string
    {
        $html="<h1>Titre:</h1>".$this->serie->__get("titre")."<br>".
            $this->serie->__get("descriptif")."<br>".
            "<h2>date d'ajout:</h2>".$this->serie->__get("dateAjout")."<br>";
            "<h2>date de sortie:</h2>".$this->serie->__get("dateSortie")."<br>". $this->renderEpisodes();

        return $html;
}

    private function renderEpisodes():string
    {
        $html="";
        //TODO a faire;
        return $html;
    }
    }