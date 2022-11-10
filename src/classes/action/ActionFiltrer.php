<?php

namespace iutnc\netvod\action;

use iutnc\netvod\bd\ConnectionFactory;
use iutnc\netvod\NetVOD\Serie;

class ActionFiltrer extends Action
{
    //verifier si l'action est genre ou public



    public function execute(): string
    {
        $html="bonjour";
        //si l action est genre
        if(isset($_GET['genre'])){
            $genre=$_GET['genre'];
            $listeSerie= $this->genererSerieParGenre($genre);
        }
        return $html;

    //stocker les series au bon genre dans une liste
    //afficher la liste
    }

    private function genererSerieParGenre($genre)
    {
        $db = ConnectionFactory::makeConnection();
        $query = "SELECT * FROM serie ";
        $result = $db->prepare($query);
        $result->execute();

        $listeSerie = [];
        while ($datas = $result->fetch(\PDO::FETCH_ASSOC)) {
            if($datas['genre']==$genre){
                $serie = new Serie($datas['titre'], $datas['img'], $datas['descriptif'], $datas['annee'], $datas['date_ajout'], $datas['id']);
                $listeSerie[]=$serie;
            }
            return $listeSerie;

        }
    }
}