<?php

namespace iutnc\netvod\render;

use iutnc\netvod\NetVOD\Serie;

class RenderSerie implements Renderer
{
    protected Serie $serie;

    public function __construct(Serie $serie)
    {
        $this->serie = $serie;
    }

    /**
     * @param $selector 0 pour compact, 1 pour complet
     * @return string
     */
    public function render($selector = 0): string
    {
        $html = "";
        switch ($selector) {
            case 0:
                $html = $this->renderCompact();
                break;
            case 1:
                $html = $this->renderComplet();
                break;
        }
        return $html;
    }

    private function renderCompact(): string
    {
        $img = $this->serie->__get('cheminImage');
        $txt = $this->serie->__get("titre");
        $html = "<img src='$img' > <div id='txtbtn'> $txt </div>" ;
        //style='width:150px;height:120px;'
        return $html;
    }

    private function renderComplet(): string
    {
        $html = "<div id='detailserie'><h2><u>Titre :</u> " . $this->serie->__get("titre") . "</h2> <br>" .
            $this->serie->__get("descriptif") . "<br>" .
            "<h3>date d'ajout : " . $this->serie->__get("dateAjout") . " </h3><br>";
            "<h3>date de sortie : " . $this->serie->__get("dateSortie") . " </h3><br></div>";

        return $html;
    }

    /*private function renderEpisodes():string
    {
        $html="";
        foreach ($this->serie->__get("listeEpisodes") as &$value){
            $html.=new RenderEpisode($value)->$this->render()
        }
        return $html;
    }*/
}