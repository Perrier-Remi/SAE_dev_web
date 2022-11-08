<?php

namespace iutnc\netvod\action;

use iutnc\netvod\NetVOD\Serie;
use iutnc\netvod\render\RenderSerie;
use iutnc\netvod\bd\ConnectionFactory;

class ActionCatalogue extends Action
{


    public function execute(): string
    {
        $html = "";

            $db = ConnectionFactory::makeConnection();
            $query ="SELECT * FROM serie ";
            $result = $db->prepare($query);
            $result->execute();
            $html .= "<form id='accueil' method='post' enctype='multipart/form-data' action = ''>";
            while($datas = $result->fetch(\PDO::FETCH_ASSOC)) {
                $serie = new Serie($datas['titre'],$datas['img'],$datas['descriptif'],$datas['annee'],$datas['date_ajout'],$datas['id']);
                $render = new RenderSerie($serie);
                $id_serie=$datas['id'];
                $data = $render->render();
                $html .= "<li><button formaction='index.php?action=serie&id=$id_serie'>$data</button></li>";
            }
        $result->closeCursor();
        $html.= '</form></center></ul>';

        return $html;
    }
}