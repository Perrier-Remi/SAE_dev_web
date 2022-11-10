<?php

namespace iutnc\netvod\action;

use iutnc\netvod\NetVOD\Serie;
use iutnc\netvod\render\RenderSerie;
use iutnc\netvod\bd\ConnectionFactory;

class ActionCatalogue extends Action
{


    public function execute(): string
    {
        $html = <<<end
                    <form id="search" action ="" method = "get">
                <input id="searchbar" type = "search" name = "terme">
                <input id="btnsearch" type = "submit" name = "action" value = "rechercher">
                </form>
                end;

        $html .= " <form id=\"f1\" method=\"post\" action='?action=catalogue'>
                        <div style=\"text-align: center\"> 
                        <label for='tri-select'> Trier par : </label>
                        <select name='tri' id='tri-select'>
                            <option value=''> </option>
                            <option value='titre'>Titre</option>
                            <option value='dateAjout'>Date d'ajout sur la plateforme</option>
                            <option value='annee'>Annee</option>
                            <option value='noteMoyenne'>Note moyenne</option>
                        </select>
                        <button type=\"submit\" name=\"commentaire\" value=\"vrai\"> Trier </button> </div>
                        </form>";

        try {
            $db = ConnectionFactory::makeConnection();
        } catch (\Exception $e) {
            $html .= "<p> Connection à la base de données impossible</p>";
        }

        if (isset($_POST['tri'])) {
            $tri = $_POST['tri'];
        } else {
            $tri = "";
        }

        switch ($tri) {
            case 'titre':
                $html .= $this->tri($db, "SELECT * FROM serie ORDER BY titre ASC");
                break;
            case 'dateAjout':
                $html .= $this->tri($db, "SELECT * FROM serie ORDER BY date_ajout ASC");
                break;
            case 'annee':
                $html .= $this->tri($db, "SELECT * FROM serie ORDER BY annee ASC");
                break;
            case 'noteMoyenne':
                $html .= $this->tri($db, "SELECT * FROM serie ORDER BY noteMoyenne ASC");
                break;
            default:
                $html .= $this->tri($db, "SELECT * FROM serie");
                break;
        }

        return $html;
    }

    public function tri(\PDO $db, string $query) : string {
        $html = "";
        $result = $db->prepare($query);
        $result->execute();
        $html .= "<form id='accueil' class='serie' method='post' enctype='multipart/form-data' action = ''>";
        while ($datas = $result->fetch(\PDO::FETCH_ASSOC)) {
            $serie = new Serie($datas['titre'], $datas['img'], $datas['descriptif'], $datas['annee'], $datas['date_ajout'], $datas['id']);
            $render = new RenderSerie($serie);
            $id_serie = $datas['id'];
            $data = $render->render();
            $html .= "<li><button formaction='index.php?action=serie&id=$id_serie'>$data</button></li>";
        }
        $result->closeCursor();
        $html .= '</form></center></ul>';

        return $html;
    }
}