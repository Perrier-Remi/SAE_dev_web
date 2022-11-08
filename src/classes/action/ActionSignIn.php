<?php

namespace iutnc\netvod\action;

use iutnc\netvod\exception\AuthException as AuthException;
use iutnc\netvod\authentification\Auth as Auth;

class ActionSignIn extends Action
{

    public function execute(): string
    {

        $html = "";
        if ($this->http_method === 'GET') {
            $html =
                " <form id=\"f1\" method=\"post\" action='?action=sign-in'>
            <input type=\"email\" placeholder=\"email\" name=\"email\">
            <input type=\"password\" placeholder=\"*****\" name=\"pswd\">
            <div style=\"text-align: center\"> 
            <button type=\"submit\" name=\"connexion\" value=\"vrai\"> Connexion </button> </div>
            <a href='index.php?action=add-user'>s'inscrire</a>
            </form>";
        } else {
            try {
                Auth::authenticate($_POST['email'], $_POST['pswd']);
                $html = "<p> Connexion réussie </p>";
            } catch (AuthException $e) {
                $html = "<p> Erreur dans l'email ou le mot de passe </p>";
            }
        }
        return $html;
    }
}