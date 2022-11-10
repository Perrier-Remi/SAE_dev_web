<?php

namespace iutnc\netvod\action;

use iutnc\netvod\authentification\Auth;
use iutnc\netvod\exception\AuthException;

class ActionMotDePasseOublie extends Action
{

    public function execute(): string
    {
        if ($this->http_method === 'GET') {
            $html =
                " <form id=\"f1\" method=\"post\" action='?action=mdp-oublie'>
            <input type=\"email\" placeholder=\"email\" name=\"email\">
            <div style=\"text-align: center\"> 
            <button type=\"submit\" name=\"mdp-oublie\" value=\"vrai\"> Changer de mot de passe </button> </div>
            </form>";
        } else {
            try {
                if (! filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) ) {
                    throw new AuthException("email invalide");
                }
                $mail = $_POST['email'];

                Auth::verifierCompteExiste($mail);

                $_SESSION['email_mdp_oublie'] = $mail;

                //création d'un token pour vérifier que c'est le bon utilisateur qui veut changer de mot de passe
                $chaineAleatoire = bin2hex(random_bytes(64));
                $dateExpiration = date('YmdHis', strtotime("+1 hour")); // le token a une validité de une heure
                $tokenServeur = $dateExpiration."|".$chaineAleatoire;
                $_SESSION['token_changement_mdp'] = $tokenServeur;

                $html = "<p>cliquez sur le lien pour changer de mot de passe : <a style='color:dodgerblue' href='index.php?action=mdp-oublie-confirmation&token=$tokenServeur'>changer le mot de passe</a></p>";
            } catch (AuthException $e) {
                $message = $e->getMessage();
                $html = "<p style='color:red'> Problème le changement de mot de passe du compte : $message </p>";
            }
        }
        return $html;    }
}
