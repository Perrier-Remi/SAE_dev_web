<?php

namespace iutnc\netvod\action;

class ActionChercher extends Action
{

    public function execute(): string
    {
        return $_GET["terme"];
        }
}