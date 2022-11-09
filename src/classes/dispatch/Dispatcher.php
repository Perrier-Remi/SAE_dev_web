<?php

namespace iutnc\netvod\dispatch;

use iutnc\netvod\action\ActionAjouterFavoris;
use iutnc\netvod\action\ActionCatalogue;
use iutnc\netvod\action\AccueilAction;
use iutnc\netvod\action\ActionConfirmationInscription;
use iutnc\netvod\action\ActionCommentaires;
use iutnc\netvod\action\ActionDeconnecter;
use iutnc\netvod\action\ActionEpisode;
use iutnc\netvod\action\ActionProfil;
use iutnc\netvod\action\ActionInscription;
use iutnc\netvod\action\ActionSerie;
use iutnc\netvod\action\ActionSignIn as ActionSignIn;

class Dispatcher
{
    private string $action;

    /**
     * @param string $action
     */
    public function __construct(string $action)
    {
        $this->action = $action;
    }

    public function run(): void
    {
        $current= $_SERVER['QUERY_STRING'];
        if (!isset($_SESSION['url_prec'])){
            $_SESSION['url_actuel']=$current;
            $_SESSION['url_prec']=$current;
        }else{
            $_SESSION['url_prec']=$_SESSION['url_actuel'];
            $_SESSION['url_actuel']=$current;
        }
        switch ($this->action) {
            case 'add-user':
                $act = new ActionInscription();
                $this->renderPage($act->execute());
                break;
            case 'sign-in':
                $act = new ActionSignIn();
                $this->renderPage($act->execute());
                break;
            case 'accueil':
                $act = new AccueilAction();
                $this->renderPage($act->execute());
                break;
            case 'catalogue':
                $act = new ActionCatalogue();
                $this->renderPage($act->execute());
                break;
            case 'serie':
                $act = new ActionSerie();
                $this->renderPage($act->execute());
                break;
            case 'episode':
                $act = new ActionEpisode();
                $this->renderPage($act->execute());
                break;
            case 'deconnecter':
                $act = new ActionDeconnecter();
                $this->renderPage($act->execute());
                break;
            case 'confirmer-inscription':
                $act = new ActionConfirmationInscription();
                $this->renderPage($act->execute());
                break;
            case 'commentaires':
                $act = new ActionCommentaires();
                $this->renderPage($act->execute());
                break;
            case 'profil':
                $act = new ActionProfil();
                $this->renderPage($act->execute());
                break;
            default:
                $this->renderPage("<div style=\"text-align: center;\"> Bonjour! </div>");
                break;
        }
    }

    public function renderPage(string $html): void
    {
        $btnRetour="<button formaction=''>Retour</button>";
        print(
        <<<end
        <html lang=\"fr\">
        <head>
        <meta charset=\"utf-8\">
        <title>NetVOD</title>
        <link rel="stylesheet" type="text/css" href="src/classes/styles/styleAction.css"/> 
        </head>
        <body>
            <h1>NETVOD</h1>
            <form name='menu' action="" method='get'>

                <button class="btnsubmit" type="submit" name="action" value="accueil">Accueil</button>
                <button class="btnsubmit" type="submit" name="action" value="catalogue">Catalogue</button>
                <button class="btnsubmit" type="submit" name="action" value="deconnecter">Se déconnecter</button>
            </form>
            <br>

            $btnRetour
        
            $html
        </body>
        </html>
        end);

    }


}