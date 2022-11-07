<?php

namespace iutnc\netvod\action;

use iutnc\netvod\exception\AuthException as AuthException;
use iutnc\netvod\authentification\Auth as Auth;

class ActionInscription extends Action {

    public function execute(): string {

        $html = "";
        if ($this->http_method === 'GET') {
            $html =
                " <form id=\"f1\" method=\"post\" action='?action=add-user'>
            <input type=\"email\" placeholder=\"email\" name=\"email\">
            <input type=\"password\" placeholder=\"mot de passe\" name=\"pswd\">
            <input type=\"password\" placeholder=\"confirmez le mot de passe\" name=\"pswd_confirm\">
            <div style=\"text-align: center\"> 
            <button type=\"submit\" name=\"connexion\" value=\"vrai\"> Register </button> </div>
            </form>";
        } else {
            try {
                //sanitizer les input
                Auth::register($_POST['email'], $_POST['pswd'], $_POST['pswd_confirm']);
                $html = "<p> Compte crée avec succès </p>";
            } catch (AuthException $e) {
                $message = $e->getMessage();
                $html = "<p> Problème avec la création du compte : $message </p>";
            }
        }
        return $html;
    }
}