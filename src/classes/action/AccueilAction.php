<?php

namespace iutnc\netvod\action;


use iutnc\netvod\NetVOD\Serie;
use iutnc\netvod\render\RenderSerie;

class AccueilAction extends Action
{

    public function execute(): string
    {
        $retour = <<<end
                <h3>Séries que vous avez aimé</h3>
                <center>
                <ul>
               end;
        if (isset($_SESSION['user'])){
            $db = ConnectionFactory::makeConnection();
            $query ="SELECT * FROM useraime WHERE id=?";
            $result = $db->prepare($query);
            $result->execute([$_SESSION['user']]);
            while($datas = $result->fetch(\PDO::FETCH_ASSOC)) {
                // creer series
                $id_serie = $datas['id_serie'];
                $query2 ="SELECT * FROM serie WHERE id=?";
                $result2 = $db->prepare($query2);
                $result2->execute([$id_serie]);
                $res = $result2->fetch(\PDO::FETCH_ASSOC);
                $serie = new Serie($res['titre'],$res['img'],$res['descriptif'],$res['annee'],$res['date_ajout']);
                $render = new RenderSerie($serie);
                $data = $render->render();
                $retour .= "<li><button formaction='index.php?action=serie&id=$id_serie'>$data</button></li>";
            }
            $result->closeCursor();
            $retour .= '</center></ul>';

        }
        return $retour;
    }
}