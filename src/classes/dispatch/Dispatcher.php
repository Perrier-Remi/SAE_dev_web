<?php

namespace iutnc\netvod\dispatch;

use iutnc\netvod\action\ActionAjouterFavoris;
use iutnc\netvod\action\ActionCatalogue;
use iutnc\netvod\action\AccueilAction;
use iutnc\netvod\action\ActionDeconnecter;
use iutnc\netvod\action\ActionEpisode;
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

            default:
                $this->renderPage("<div style=\"text-align: center;\"> Bonjour! </div>");
                break;
        }
    }

    public function renderPage(string $html): void
    {
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
                <input class="btnsubmit" type="submit" name="action" value="accueil">
                <input class="btnsubmit" type="submit" name="action" value="catalogue">
                <input class="btnsubmit" type="submit" name="action" value="deconnecter">
            </form>
            <br>
        
            $html
        </body>
        </html>
        end);

    }


}