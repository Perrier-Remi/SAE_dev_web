<?php

namespace iutnc\netvod\action;

use iutnc\netvod\NetVOD\Serie;
use iutnc\netvod\render\RenderSerie;
use iutnc\netvod\bd\ConnectionFactory;

class AccueilAction extends Action
{

    public function execute(): string
    {
        $retour = <<<end
                <h2>Séries que vous avez aimé</h2>
                <center>
                <ul>
               end;
        if (isset($_SESSION['id_user'])){
            $db = ConnectionFactory::makeConnection();

            $retour .= $this->affichageSeries($db, "SELECT * FROM useraime WHERE id_user=?", false);
            $retour .= <<<end
                <h2>Vos séries en cours</h2>
                <center>
                <ul>
               end;
            $retour .= $this->affichageSeries($db, "SELECT * FROM serieEnCours WHERE id_user=?", true);
            $retour .= <<<end
                <h2>Les séries que vous avez terminées</h2>
                <center>
                <ul>
               end;
            $retour .= $this->affichageSeries($db, "SELECT * FROM serieDejaVisionnee WHERE id_user=?", false);


        }
        return $retour;
    }

    public function affichageSeries(\PDO $db, string $query, bool $enCours) : string {
        $retour = "";
        $result = $db->prepare($query);
        $result->execute([$_SESSION['id_user']]);
        $retour .= "<form id='accueil' class='serie' method='post' enctype='multipart/form-data' action = ''>";
        while($datas = $result->fetch(\PDO::FETCH_ASSOC)) {
            // creer series
            $id_serie = $datas['id_serie'];
            $query2 ="SELECT * FROM serie WHERE id=?";
            $result2 = $db->prepare($query2);
            $result2->execute([$id_serie]);
            $res = $result2->fetch(\PDO::FETCH_ASSOC);
            $serie = new Serie($res['titre'],$res['img'],$res['descriptif'],$res['annee'],$res['date_ajout'],$id_serie);
            $render = new RenderSerie($serie);
            $data = $render->render();
            if (!$enCours) {
                $retour .= "<li><button id='fin' formaction='index.php?action=serie&id=$id_serie'>$data</button></li>";
            } else {
                // Donne accès à l'épisode courant
                $stmt = $db->prepare("SELECT id_episode FROM episodeEnCours WHERE id_serie = ? AND actuel = ?");
                $stmt->execute([$id_serie, true]);
                $id_episode = $stmt->fetch()[0];
                $retour .= "<li><button id='fin' formaction='index.php?action=episode&id_episode=$id_episode   '>$data</button></li>";
            }
        }
        $result->closeCursor();
        $retour .= '</fom></center></ul>';
        return $retour;
    }
}