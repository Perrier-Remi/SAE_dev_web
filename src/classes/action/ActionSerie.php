<?php

namespace iutnc\netvod\action;

use iutnc\netvod\bd\ConnectionFactory;
use iutnc\netvod\NetVOD\Serie;
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

            }
            else{
                $html="<p> Pas de serie choisi </p>";
            }

        }
        return $html;

    }
}