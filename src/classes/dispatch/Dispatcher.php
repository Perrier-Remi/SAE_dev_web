<?php

namespace iutnc\netvod\dispatch;

use iutnc\deefy\action\ActionCatalogue;
use iutnc\netvod\action\AccueilAction;
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
            case 'acceuil':
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
            default:
                $this->renderPage("<div style=\"text-align: center;\"> Bonjour! </div>");
                break;
        }
    }

    public function renderPage(string $html): void
    {
        print(
        <<<end
        ("<html lang=\"fr\">
        <head>
        <meta charset=\"utf-8\">
        <title>TD 15</title>
        <link rel=\"stylesheet\" href=\"style.css\">
        </head>
        <body>
            $html
        </body>
        </html>
        end);

    }


}