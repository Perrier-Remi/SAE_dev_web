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
        if (isset($_SESSION['user'])){
            $db = ConnectionFactory::makeConnection();
            $query ="SELECT * FROM useraime WHERE id_user=?";
            $result = $db->prepare($query);
            $user = unserialize($_SESSION['user']);
            $result->execute([$user->__get('id')]);
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
                $retour .= "<li><button formaction='index.php?action=serie&id=$id_serie'>$data</button></li>";
            }
            $result->closeCursor();
            $retour .= '</fom></center></ul>';

        }
        return $retour;
    }
}