<?php

namespace iutnc\netvod\action;

use iutnc\netvod\exception\AuthException as AuthException;
use iutnc\netvod\authentification\Auth as Auth;

class ActionSignIn extends Action
{

    public function execute(): string
    {
        $html='';
        if ($this->http_method === 'GET') {
            $html =
                " <form id=\"f1\" method=\"post\" action='?action=sign-in'>
            <input type=\"email\" placeholder=\"email\" name=\"email\">
            <input type=\"password\" placeholder=\"*****\" name=\"pswd\">
            <div style=\"text-align: center\"> 
            <button type=\"submit\" name=\"connexion\" value=\"vrai\"> Connexion </button> </div>
            <a href='index.php?action=add-user'>s'inscrire</a>
            <a href='index.php?action=mdp-oublie'>mot de passe oublié</a>
            </form>";

        } else {
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
                $_SESSION['co']='success';
                $html = "<p> Connexion réussie </p>";
                $act=new ActionLoadProfil();
                $html.=$act->execute();
                // ajout du texte de l action load profil
            } catch (AuthException $e) {
                $message = $e->getMessage();
                $html .= "<p>$message</p>";
            }
        }
        return $html;
    }
}