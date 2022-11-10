<?php

namespace iutnc\netvod\action;

use iutnc\netvod\authentification\Auth;
use iutnc\netvod\exception\AuthException;

class ActionMotDePasseOublieConfirmation extends Action
{

    public function execute(): string
    {
        if ($this->http_method === 'GET') {
            try {


                // vérification de la validité du token
                if (!filter_var($_GET['token'], FILTER_SANITIZE_STRING)) {
                    throw new AuthException("token invalide");
                }

                $tokenUser = $_GET['token'];
                $tokenServeur = $_SESSION['token_changement_mdp'];

                $dateServeur = date('YmdHis', strtotime("now"));
                $dateTokenExpiration = explode("|", $tokenUser)[0];

                if ($dateTokenExpiration < $dateServeur) throw new AuthException("le token a expiré");

                $valeurTokenUser = explode("|", $tokenUser)[1];
                $valeurTokenServeur = explode("|", $tokenServeur)[1];
                if ($valeurTokenServeur !== $valeurTokenUser) throw new AuthException("les tokens ne sont pas identiques");

                // si tous les tests sont passés alors on a l'affichage du formulaire pour changer le mot de passe
                $html =
                    " <form id=\"f1\" method=\"post\" action='?action=mdp-oublie-confirmation'>
                <input type=\"password\" placeholder=\"mot de passe\" name=\"pswd\">
                <input type=\"password\" placeholder=\"confirmez le mot de passe\" name=\"pswd_confirm\">
                <div style=\"text-align: center\"> 
                <button type=\"submit\" name=\"inscription\" value=\"vrai\"> Changer le mot de passe </button> </div>
                </form>";
            } catch (AuthException $e) {
                $message = $e->getMessage();
                $html = "<p style='color:red'> Problème avec la création du compte : $message </p>";
            }
        } else {
            try {
                if (! filter_var($_POST['pswd'], FILTER_SANITIZE_STRING) ) {
                    throw new AuthException("mot de passe invalide");
                }
                if (! filter_var($_POST['pswd_confirm'], FILTER_SANITIZE_STRING) ) {
                    throw new AuthException("mot de passe de confirmation invalide");
                }
                $password = $_POST['pswd'];
                $password_confirm = $_POST['pswd_confirm'];

                Auth::changerMotDePasse($_SESSION['email_mdp_oublie'], $password, $password_confirm);
                $html = "<p style='color:green'>Le mot de passe a bien été changé</p>";
            } catch (AuthException $e) {
                $message = $e->getMessage();
                $html = "<p style='color:red'> Problème avec la création du compte : $message </p>";
            }
        }
        return $html;
    }
}