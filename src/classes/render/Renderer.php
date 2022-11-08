<?php

namespace iutnc\netvod\render;

interface Renderer
{
    public function render($selector=0) : string ;
}
