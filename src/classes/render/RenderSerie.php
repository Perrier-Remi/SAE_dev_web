<?php
namespace iutnc\deefy\render;
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
        $html="<img src=".$this->serie->;


        return $html;

    }
}