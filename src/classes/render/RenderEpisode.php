<?php

namespace iutnc\netvod\render;

use iutnc\netvod\NetVOD\Episode;

class RenderEpisode implements Renderer
{
    private Episode $episode;

    /**
     * @param $episode
     */
    public function __construct($episode)
    {
        $this->episode = $episode;
    }


    public function render($selector=0): string
    {
        $html = "";
        // si on veut un affichage pour la série
        if ($selector === 1) {
            $html = "<p> Episode". $this->episode->__get('numero')." : ". $this->episode->__get('titre')." </p>";
        } else {
            $html = "<div style=\"text-align:center\"><h1>". $this->episode->__get('titre')." </h1></div> <br> <br>
             <div style=\"text-align:center\">". $this->episode->__get('resume')." </div> <br>
             <div style=\"text-align:center\">". $this->episode->__get('duree')." secondes </div> <br>
             <div style=\"text-align:center\"> <video controls width='600'> <source src='video/". $this->episode->__get('cheminFich')."' id='currentEpisode'> </video> </div>";
        }
        return $html;
    }




}