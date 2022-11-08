<?php

namespace iutnc\netvod\action;

use iutnc\netvod\exception\AuthException as AuthException;
use iutnc\netvod\authentification\Auth as Auth;

class ActionSignIn extends Action
{

    public function execute(): string
    {

        $html =
            " <form id=\"f1\" method=\"post\" action='?action=sign-in'>
            <input type=\"email\" placeholder=\"email\" name=\"email\">
            <input type=\"password\" placeholder=\"*****\" name=\"pswd\">
            <div style=\"text-align: center\"> 
            <button type=\"submit\" name=\"connexion\" value=\"vrai\"> Connexion </button> </div>
            <a href='index.php?action=add-user'>s'inscrire</a>
            </form>";
        if ($this->http_method === 'POST') {
            try {
                if (! filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) ) {
                    throw new AuthException("email invalide");
                }
                if (! filter_var($_POST['pswd'], FILTER_SANITIZE_STRING) ) {
                    throw new AuthException("mot de passe invalide");
                }
                $mail = $_POST['email'];
                $password = $_POST['pswd'];
                Auth::authenticate($mail, $password);
                $html = "<p> Connexion réussie </p>";
            } catch (AuthException $e) {
                $message = $e->getMessage();
                $html .= <<< END
                    <p>$message</p>
                END;
            }
        }
        return $html;
    }
}