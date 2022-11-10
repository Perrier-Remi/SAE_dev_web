<?php

namespace iutnc\netvod\dispatch;

use iutnc\netvod\action\ActionAjouterFavoris;
use iutnc\netvod\action\ActionCatalogue;
use iutnc\netvod\action\AccueilAction;
use iutnc\netvod\action\ActionChercher;
use iutnc\netvod\action\ActionInscriptionConfirmation;
use iutnc\netvod\action\ActionInscription;
use iutnc\netvod\action\ActionCommentaires;
use iutnc\netvod\action\ActionDeconnecter;
use iutnc\netvod\action\ActionEpisode;
use iutnc\netvod\action\ActionMotDePasseOublie;
use iutnc\netvod\action\ActionMotDePasseOublieConfirmation;
use iutnc\netvod\action\ActionProfil;
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
                $act = new ActionInscriptionConfirmation();
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
            case 'rechercher':
                $act=new ActionChercher();
                $this->renderPage($act->execute());
                break;
            case 'mdp-oublie':
                $act=new ActionMotDePasseOublie();
                $this->renderPage($act->execute());
                break;
            case 'mdp-oublie-confirmation' :
                $act=new ActionMotDePasseOublieConfirmation();
                $this->renderPage($act->execute());
                break;
            default:
                $this->renderPage("<div style=\"text-align: center;\"> Bonjour par defaut! </div>");
                break;
        }
    }

    public function renderPage(string $html): void
    {
        $btnRetour='';
        if (isset($_GET['retour']) && $_GET['action']!='episode'){
            array_pop($_SESSION['list_url']);
            array_pop($_SESSION['list_url']);
        }
        if ($_GET['action']=='serie' || $_GET['action']=='episode'){
            //$url = end($_SESSION['list_url']);
            $url = $_SESSION['list_url'][count($_SESSION['list_url'])-2];
            if ($url==""){$url = 'index.php?'.$_SERVER['QUERY_STRING'];}
            $btnRetour.="<form method='post'>";
            $btnRetour.="<button id='btnRetour' formaction='index.php?$url&retour' ><img src='src/classes/styles/retour.png'></button>";
            $btnRetour.="</form>";
        }
        print(
        <<<end
        <html lang=\"fr\">
        <head>
        <meta charset=\"utf-8\">
        <title>NetVOD</title>
        <link rel="stylesheet" type="text/css" href="src/classes/styles/styleAction.css"/> 
        </head>
        <body>
            <h1 id="GrandTitre">NETVOD</h1>
            
           
            <form name='menu' id="menu" action="" method='get'>
                <button id='premierBtn'class="btnsubmit" type="submit" name="action" value="accueil">Accueil</button>
                <button class="btnsubmit" type="submit" name="action" value="catalogue">Catalogue</button>
                <button class="btnsubmit" type="submit" name="action" value="deconnecter">Se déconnecter</button>
                <button  id="dernierBtn" class="btnsubmit" type="submit" name="action" value="profil">Profil</button>
            </form>
            $btnRetour
            <br>
            $html
        </body>
        </html>
        end);

    }


}