<?php

namespace iutnc\netvod\action;

use iutnc\netvod\bd\ConnectionFactory;

class ActionLoadProfil extends Action
{

    public function execute(): string
    {
        $html='';
        $db=null;
        try {
            $db = ConnectionFactory::makeConnection();
        } catch (\Exception $e) {
            $html .= "<p> Connection à la base de données impossible</p>";
        }
        if ($db!=null){
            if(isset($_POST['nom'])&& isset($_POST['prenom'])){
                $query_email = "select count(*) from profils";
                $stmt = $db->prepare($query_email);
                $stmt->execute();
                $res=$stmt->fetch();
                $query_email = "INSERT INTO profils (email,id_user,nom,prenom) VALUES (?,?,?,?)";
                $stmt = $db->prepare($query_email);
                $nom2 = filter_var($_POST['nom'],FILTER_SANITIZE_STRING);
                $prenom2 = filter_var($_POST['prenom'],FILTER_SANITIZE_STRING);
                $stmt->execute([$_SESSION['email'],$res[0]+1,$nom2,$prenom2]);
                $html.='profil ajouté';
            }
            else if (isset($_POST['number'])){
                $query_email = "select * from profils where email = ?";
                $stmt = $db->prepare($query_email);
                $stmt->execute([$_SESSION['email']]);
                $stmt->setFetchMode(\PDO::FETCH_ASSOC);
                $entier=0;
                $res='';
                while ($entier<$_POST['number']){
                    $res=$stmt->fetch();
                    $entier++;
                }

                $tab = ['profils','useraime','serieDejaVisionnee','Commentaires','episodeEnCours','serieEnCours'];
                foreach ($tab as $data){
                    $query_email = "DELETE FROM ".$data." WHERE id_user = ?";
                    $stmt = $db->prepare($query_email);
                    $stmt->execute([$res['id_user']]);
                }
            }
            else if(array_key_exists('button1', $_POST)) {
                $_SESSION['id_user']=$_POST['button1'];
                $html.='profil selectionné';
            }
            else if(array_key_exists('button2', $_POST)) {
                $_SESSION['id_user']=$_POST['button2'];
                $html.='profil selectionné';
            }
            else if(array_key_exists('button3', $_POST)) {
                $_SESSION['id_user']=$_POST['button3'];
                $html.='profil selectionné';
            }
            else if(array_key_exists('button4', $_POST)) {
                $_SESSION['id_user']=$_POST['button4'];
                $html.='profil selectionné';
            }
            $html .= <<<end
               <form method="post">
                    <input type="submit" name="creation" value="Creer un profil" />
                    <input type="submit" name="suppression" value="supprimer un profil" /><br>
            end;

            $query_email = "select * from profils where email = ?";
            $stmt = $db->prepare($query_email);
            $stmt->execute([$_SESSION['email']]);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $nb=1;
            $desactivation = '';
            if (isset($_SESSION['id_user'])) $desactivation='disabled';
            while ($data = $stmt->fetch()) {
                $id=$data['id_user'];
                $nom=$data['nom'];
                $prenom=$data['prenom'];
                if ($nb==3){$html.='<br>';}
                $html.="<button class='profil' type='submit' name='button$nb' value='$id'".$desactivation."><img class='profile' src='src/classes/styles/cadre-photo.png'>Profil $nb : $nom $prenom</button>";
                $nb++;
            }
            $html .= "</form>";
            if(array_key_exists('creation', $_POST)) {
                if ($nb>4){
                    $html.='on ne peut pas ajouter de nouveau profils';
                }else{
                    $html.=<<<end
                        <form method="post">
                            Nom :<input type="text" name="nom"/>
                            Prenom :<input type="text" name="prenom"/>
                            <input type="submit" />
                        </form>
                    end;

                }
            }
            else if(array_key_exists('suppression', $_POST)) {
                if ($nb==1){
                    $html.='on ne peut pas supprimer de profils';
                }else{
                    $html.=<<<end
                        <form method="post">
                            numero du profil a supprimer : <input type="number" name="number"/>
                            <input type="submit" />
                        </form>
                    end;
                }
            }

        }
        return $html;
    }

}