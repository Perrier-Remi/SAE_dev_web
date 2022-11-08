<?php

namespace iutnc\deefy\action;

use iutnc\netvod\NetVOD\Serie;
use iutnc\netvod\render\RenderSerie;

class ActionCatalogue extends Action
{


    public function execute(): string
    {
        $html = "";
        if ($this->http_method == 'GET'){
           /* $html = "<form method='post' action ='index.php?action=add-playlist'>";
            $html.= "<input type='text' name='nomPlaylist' placeholder=\"<Nom de Playlist>\">";
            $html.= "<button type=\"submit\">Valider</button>";
            $html.= "</form>";*/
            $db = ConnectionFactory::makeConnection();
            $query ="SELECT  FROM useraime WHERE id=?";
            $result = $db->prepare($query);
           // $result->execute([$_SESSION['user']]);

            $nom='';
            while($datas = $result->fetch(\PDO::FETCH_ASSOC)) {
                $serie = new Serie($datas['titre'],$datas['img'],$datas['descriptif'],$datas['date_ajout'],$datas['aneee']);
                $renderSerie=new RenderSerie();
                $renderSerie->render(0);
                $html.="<li action='index.php?action=catalogue'>".$renderSerie."</li><br>";
            }
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