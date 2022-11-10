<?php

namespace iutnc\netvod\action;

use iutnc\netvod\exception\AuthException;
use iutnc\netvod\authentification\Auth;

class ActionInscription extends Action {

    public function execute(): string {

        if ($this->http_method === 'GET') {
            $html =
                " <form id=\"f1\" method=\"post\" action='?action=add-user'>
            <input type=\"email\" placeholder=\"email\" name=\"email\">
            <input type=\"password\" placeholder=\"mot de passe\" name=\"pswd\">
            <input type=\"password\" placeholder=\"confirmez le mot de passe\" name=\"pswd_confirm\">
            <div style=\"text-align: center\"> 
            <button type=\"submit\" name=\"inscription\" value=\"vrai\"> Inscription </button> </div>
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

                Auth::verifierCompteExistePas($mail);
                Auth::checkCredentials($mail, $password, $password_confirm);
                $_SESSION['email_inscription'] = $mail;
                $_SESSION['hash_mdp_inscription']= password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

                // création du token de validation d'inscription du compte
                $chaineAleatoire = bin2hex(random_bytes(64));
                $dateExpiration = date('YmdHis', strtotime("+1 hour")); // le token a une validité de une heure
                $tokenServeur = $dateExpiration."|".$chaineAleatoire;
                $_SESSION['token_inscription'] = $tokenServeur;

                $html = "<p>cliquez sur ce lien pour confirmer l'inscription : <a style='color:dodgerblue' href='index.php?action=confirmer-inscription&token=$tokenServeur'>confirmer l'inscription</a></p>";
            } catch (AuthException $e) {
                $message = $e->getMessage();
                $html = "<p style='color:red'> Problème avec la création du compte : $message </p>";
            }
        }
        return $html;
    }
}