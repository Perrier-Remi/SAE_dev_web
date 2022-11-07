<?php

namespace iutnc\netvod\render;

use iutnc\deefy\NetVOD\Serie;

class RenderSerie implements Renderer
{
    protected Serie $serie;
    public function __construct(Serie $serie)
    {
        $this->serie=$serie;
    }

    public function render($selector=0): string
    {   switch($selector){
        case 0:
            $this->renderCompact();
    }
        $html = "";
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
            "<h2>genre:</h2>".$this->serie->__get("descriptif")."<br>";
        return $html;
}