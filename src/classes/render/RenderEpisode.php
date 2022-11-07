<?php

namespace iutnc\netvod\render;

class RenderEpisode implements Renderer
{
    private \Episode $episode;

    /**
     * @param $episode
     */
    public function __construct($episode)
    {
        $this->episode = $episode;
    }


    public function render(int $selector): string
    {
        $html = "";
        // si on veut un affichage pour la série
        if ($selector === 1) {
            $html = "<p> Episode". $this->episode->__get('numero')." : ". $this->episode->__get('titre')." </p>";
        } else {
            $html = "<div style=\"text-align:center\">". $this->episode->__get('titre')." </div>
             <div style=\"text-align:center\">". $this->episode->__get('resume')." </div>
             <div style=\"text-align:center\">". $this->episode->__get('titre')." secondes </div>
             <div style=\"text-align:center\"> <video controls width='600'> <source src='". $this->episode->__get('cheminFich')."'> </video> </div>";
        }
        return $html;
    }
}