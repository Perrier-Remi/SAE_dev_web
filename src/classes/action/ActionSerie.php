<?php

namespace iutnc\netvod\action;

use iutnc\netvod\bd\ConnectionFactory;
use iutnc\netvod\NetVOD\Episode;
use iutnc\netvod\NetVOD\Serie;
use iutnc\netvod\render\RenderEpisode;
use iutnc\netvod\render\RenderSerie;

class ActionSerie extends Action
{


    public function execute(): string
    {
        $html = " ";
        if(isset($_SESSION['user'])) {
            if(isset($_GET['id'])) {
                $db = ConnectionFactory::makeConnection();
                $query2 = "SELECT * FROM serie WHERE id=?";
                $result2 = $db->prepare($query2);
                $result2->execute([$_GET['id']]);
                $res = $result2->fetch(\PDO::FETCH_ASSOC);
                $serie = new Serie($res['titre'], $res['img'], $res['descriptif'], $res['annee'], $res['date_ajout'],$_GET['id']);
                $render = new RenderSerie($serie);
                $html = $render->render(1);
                $query3 = "SELECT * FROM episode WHERE serie_id=?";
                $stmt = $db->prepare($query3);
                $stmt->execute([$_GET['id']]);
                $html .= "<form id='accueil' method='post' enctype='multipart/form-data' action = ''>";

                while($datas = $stmt->fetch()) {
                     $id_episode=$datas['id'];
                     $episode = new Episode($datas[1], $datas[2] , $datas[4], $datas[3], $datas[5]);
                     $renderer = new RenderEpisode($episode);
                     $renderEpisode= $renderer->render(1);
                     $html .= "<li><button formaction='index.php?action=episode&id_episode=$id_episode'>$renderEpisode</button></li>";

                 }
                $html.= '</form></center></ul>';



            }
            else{
                $html="<p> Pas de serie choisi </p>";
            }

        }
        return $html;

    }
}