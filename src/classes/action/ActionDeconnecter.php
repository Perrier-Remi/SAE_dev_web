<?php

namespace iutnc\netvod\action;

class ActionDeconnecter extends Action
{

    public function execute(): string
    {
        session_destroy();
        $retour = "<center>utilisateur déconnecté </center>";
        return $retour;
    }
}