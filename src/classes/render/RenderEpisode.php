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


    public function render(): string
    {
        $html = "";

        return $html;
    }
}