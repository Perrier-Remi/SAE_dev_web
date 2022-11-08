<?php

namespace iutnc\deefy\action;

use iutnc\netvod\NetVOD\Serie;
use iutnc\netvod\render\RenderSerie;

class ActionCatalogue extends Action
{


    public function execute(): string
    {
        $html = "";


            $db = ConnectionFactory::makeConnection();
            $query ="SELECT * FROM serie ";
            $result = $db->prepare($query);
            $result->execute();

            while($datas = $result->fetch(\PDO::FETCH_ASSOC)) {
                $serie = new Serie($datas['titre'],$datas['img'],$datas['descriptif'],$datas['annee'],$datas['date_ajout']);
                $render = new RenderSerie($serie);
                $id_serie=$datas['id'];
                $data = $render->render();
                $html .= "<li><button formaction='index.php?action=serie&id=$id_serie'>$data</button></li>";
            }
        $result->closeCursor();
        $html.= '</center></ul>';

        return $html;
    }
}