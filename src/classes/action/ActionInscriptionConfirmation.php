<?php

namespace iutnc\netvod\action;

use iutnc\netvod\authentification\Auth;
use iutnc\netvod\exception\AuthException;

class ActionInscriptionConfirmation extends Action
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
                $tokenServeur = $_SESSION['token_inscription'];

                $dateServeur = date('YmdHis', strtotime("now"));
                $dateTokenExpiration = explode("|", $tokenUser)[0];

                if ($dateTokenExpiration < $dateServeur) throw new AuthException("le token a expiré");

                $valeurTokenUser = explode("|", $tokenUser)[1];
                $valeurTokenServeur = explode("|", $tokenServeur)[1];
                if ($valeurTokenServeur !== $valeurTokenUser) throw new AuthException("les tokens ne sont pas identiques");

                // une fois que le token est vérifié, on ajoute dans l'utilisateur dans la base de donnée
                Auth::register($_SESSION['email_inscription'], $_SESSION['hash_mdp_inscription']);
                $html = "<p style='color:green'>Compte vérifié avec succès</p>";
            } catch (AuthException $e) {
                $message = $e->getMessage();
                $html = "<p style='color:red'> Problème avec la confirmation du compte : $message </p>";
            }
        }
        return $html;    }
}
