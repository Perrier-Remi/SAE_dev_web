<?php

namespace iutnc\netvod\action;

use iutnc\netvod\bd\ConnectionFactory;

class ActionLoadProfil extends Action
{

    public function execute(): string
    {
        if(array_key_exists('button1', $_POST)) {
            button1();
        }
        else if(array_key_exists('button2', $_POST)) {
            button2();
        }
        else if(array_key_exists('button3', $_POST)) {
            button3();
        }
        else if(array_key_exists('button4', $_POST)) {
            button4();
        }
        $html='';
        $db=null;
        try {
            $db = ConnectionFactory::makeConnection();
        } catch (\Exception $e) {
            $html .= "<p> Connection à la base de données impossible</p>";
        }
        if ($db!=null){
            $html .= <<<end
               <form method="post">
                    <input type="submit" name="button1" class="button" value="Button1" />
                    <input type="submit" name="button2" class="button" value="Button2" />
            end;

            $query_email = "select * from profils where email = ?";
            $stmt = $db->prepare($query_email);
            $stmt->execute([$_SESSION['email']]);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $nb=0;
            while ($data = $stmt->fetch()) {
                $html.="<button type='submit' name='button$nb' value='catalogue'>Catalogue</button>";
                $nb++;
            }



        }


        return $html;
    }
    function button1() {
        echo "This is Button1 that is selected";
    }
    function button2() {
        echo "This is Button2 that is selected";
    }

}