<?php

namespace iutnc\deefy\action;

class ActionCatalogue extends Action
{


    public function execute(): string
    {
        $html = "";
        if ($this->http_method == 'GET'){
            $html = "<form method='post' action ='index.php?action=add-playlist'>";
            $html.= "<input type='text' name='nomPlaylist' placeholder=\"<Nom de Playlist>\">";
            $html.= "<button type=\"submit\">Valider</button>";
            $html.= "</form>";
        } else {
            $nomFiltrer = filter_var($_POST['nomPlaylist'], FILTER_SANITIZE_STRING);
            $playlist = new \iutnc\deefy\audio\lists\Playlists($nomFiltrer);
            $_SESSION['playlist'] = $playlist;
            $render = new \iutnc\deefy\render\AudioListRenderer($_SESSION['playlist']);
            $html .= $render->render(3);
            $html .= "<br/>" ."<a href=\"?action=add-podcasttrack\">Ajouter une piste</a>";
        }
        return $html;
    }
}