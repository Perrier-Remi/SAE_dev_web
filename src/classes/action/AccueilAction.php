<?php

namespace iutnc\netvod\action;


use iutnc\netvod\NetVOD\Serie;

class AccueilAction extends Action
{

    public function execute(): string
    {
        $retour = <<<end
                <h1>Séries que vous avez aimé</h1>
                
               end;
        if (isset($_SESSION['user'])){
            $db = ConnectionFactory::makeConnection();
            $query ="SELECT * FROM useraime WHERE id=?";
            $result = $db->prepare($query);
            $result->execute([$_SESSION['user']]);

            $nom='';
            while($datas = $result->fetch(\PDO::FETCH_ASSOC)) {
                $serie = new Serie();

            }
            $result->closeCursor();

        }
        return $retour;
    }
}