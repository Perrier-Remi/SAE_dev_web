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
                if (! filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) ) {
                    throw new AuthException("email invalide");
                }
                if (! filter_var($_POST['pswd'], FILTER_SANITIZE_STRING) ) {
                    throw new AuthException("mot de passe invalide");
                }
                if (! filter_var($_POST['pswd_confirm'], FILTER_SANITIZE_STRING) ) {
                    throw new AuthException("mot de passe de confirmation invalide");
                }
                $mail = $_POST['email'];
                $password = $_POST['pswd'];
                $password_confirm = $_POST['pswd_confirm'];
                Auth::register($mail, $password, $password_confirm);
                $html = "<p> Compte crée avec succès </p>";
            } catch (AuthException $e) {
                $message = $e->getMessage();
                $html = "<p> Problème avec la création du compte : $message </p>";
            }
        }
        return $html;
    }
}