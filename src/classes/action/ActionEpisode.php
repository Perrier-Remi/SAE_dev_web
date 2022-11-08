<?php

namespace iutnc\netvod\action;

require_once 'src/classes/NetVOD/Episode.php';
require_once 'src/classes/bd/ConnectionFactory.php';
use iutnc\netvod\bd\ConnectionFactory;
use iutnc\netvod\NetVOD\Episode;
use iutnc\netvod\render\RenderEpisode;

class ActionEpisode extends Action
{

    public function execute(): string
    {
        $html = "";
        if (!isset($_GET['id_episode'])) {
            $html .= "<p> id episode manquant</p>";
        } else {
            try {
                $db = ConnectionFactory::makeConnection();
            } catch (\Exception $e) {
                $html .= "<p> Connection à la base de données impossible</p>";
            }

            $serie_stmt = $db->prepare("SELECT serie_id FROM episode where id=?");
            $serie_stmt->execute([$_GET['id_episode']]);
            $id_serie = $serie_stmt->fetch()[0];
            $id_user = unserialize($_SESSION['user'])->__get('id');
            $stmt_encours = $db->prepare("INSERT INTO serieEnCours(id_user, id_serie) VALUES (?,?);");
            try {
                $stmt_encours->execute([$id_user, $id_serie]);
                $html .= "<div style=\"text-align:center\"><h3> Nouvelle série </h3> </div>";
            } catch (\Exception $e) {
                $html .= "<div style=\"text-align:center\"><h3> Série en cours </h3> </div>";
            }


            $stmt = $db->prepare("SELECT * FROM episode WHERE id = ?");
            $stmt->execute([$_GET['id_episode']]);
            $donnees = $stmt->fetch();
            $episode = new Episode($donnees[1], $donnees[2] , $donnees[4], $donnees[3], $donnees[5]);
            $renderer = new RenderEpisode($episode);
            $html .= $renderer->render();


        }
        return $html;
    }
}