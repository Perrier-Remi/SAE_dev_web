<?php

namespace iutnc\netvod\action;

class ActionDeconnecter extends Action
{

    public function execute(): string
    {
        session_destroy();
        return 'deconnecter';
    }
}