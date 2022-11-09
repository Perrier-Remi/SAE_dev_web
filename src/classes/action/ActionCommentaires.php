<?php

namespace iutnc\netvod\action;

use iutnc\netvod\bd\ConnectionFactory;

class ActionCommentaires extends Action
{

    public function execute(): string
    {
        $retour = "";
        if (isset($_GET['id'])) {
            $db = ConnectionFactory::makeConnection();
            $id = $_GET['id'];
            $query2 = "SELECT commentaire FROM commentaires WHERE id_serie=?";
            $result2 = $db->prepare($query2);
            $result2->execute([$id]);
            $result2->setFetchMode(\PDO::FETCH_ASSOC);
            while ($data = $result2->fetch()) {
                $text=$data['commentaire'];
                $retour .= "<textarea type=\"text\" name=\"comm\" rows='8' cols='50' readonly='true'>$text</textarea><br>";
            }
        }else
            $retour= 'id de la serie manquant';
        if ($retour == "") $retour='pas encore de commentaire';
        return $retour;
    }
}