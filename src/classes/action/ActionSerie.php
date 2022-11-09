<?php

namespace iutnc\netvod\action;

use iutnc\netvod\bd\ConnectionFactory;
use iutnc\netvod\NetVOD\Episode;
use iutnc\netvod\NetVOD\Serie;
use iutnc\netvod\render\RenderEpisode;
use iutnc\netvod\render\RenderSerie;

class ActionSerie extends Action
{


    public function execute(): string
    {
        $html = " ";
        if (isset($_SESSION['user'])) {
            if (isset($_GET['id'])) {

                $db = ConnectionFactory::makeConnection();
                $query2 = "SELECT * FROM serie WHERE id=?";
                $result2 = $db->prepare($query2);
                $result2->execute([$_GET['id']]);
                $res = $result2->fetch(\PDO::FETCH_ASSOC);
                $serie = new Serie($res['titre'], $res['img'], $res['descriptif'], $res['annee'], $res['date_ajout'], $_GET['id']);
                $render = new RenderSerie($serie);
                $html = $render->render(1);
                $query3 = "SELECT * FROM episode WHERE serie_id=?";
                $stmt = $db->prepare($query3);
                $stmt->execute([$_GET['id']]);
                $html .= "<form id='accueil' method='post' enctype='multipart/form-data' action = ''>";

                while ($datas = $stmt->fetch()) {
                    $id_episode = $datas['id'];
                    $episode = new Episode($datas[1], $datas[2], $datas[4], $datas[3], $datas[5]);
                    $renderer = new RenderEpisode($episode);
                    $renderEpisode = $renderer->render(1);
                    $html .= "<li><button formaction='index.php?action=episode&id_episode=$id_episode'>$renderEpisode</button></li>";
                }
                $id_user = unserialize($_SESSION['user'])->__get('id');
                $id = $_GET['id'];
                if (!isset($_GET['ajFav'])) {
                    if ($this->pasEnFavori($db, $id_user, $id)) {
                        $html .= "<button formaction='index.php?action=serie&id=$id&ajFav=NONE'>Ajouter au favoris</button>";
                    } else {
                        $html .= "<button formaction='index.php?action=serie&id=$id&ajFav=OK'>Supprimer au favoris</button>";
                    }
                } else if (($this->pasEnFavori($db, $id_user, $id) && $_GET['ajFav'] != 'NONE') || ($_GET['ajFav'] == 'OK')) {
                    $html .= "<button formaction='index.php?action=serie&id=$id&ajFav=NONE'>Ajouter au favoris</button>";
                } else {
                    $html .= "<button formaction='index.php?action=serie&id=$id&ajFav=OK'>Supprimer au favoris</button>";
                }

                if (isset($_GET['ajFav'])) {
                    //si y'a un clic sur le boutton de gestion des favoris
                    if ($_GET['ajFav'] == 'NONE') {
                        //si y'a un clic sur le boutton ajouter Favoris
                        $stmt_encours = $db->prepare("INSERT INTO useraime(id_user, id_serie) VALUES (?,?);");
                        try {
                            $stmt_encours->execute([$id_user, $id]);
                            $html .= "<div style=\"text-align:center\"><h3> Serie ajoutee </h3> </div>";
                        } catch (\Exception $e) {
                            echo $e;
//            $html .= "<div style=\"text-align:center\"><h3> mal ajoutee </h3> </div>";
                        }
                    }
                    if ($_GET['ajFav'] == 'OK') {
                        //si y'a un clic sur le boutton supprimer Favoris
                        $stmt_encours = $db->prepare("DELETE FROM useraime where id_user=? and  id_serie=?;");
                        try {
                            $stmt_encours->execute([$id_user, $id]);
                            $html .= "<div style=\"text-align:center\"><h3> Serie supprime </h3> </div>";
                        } catch (\Exception $e) {
                            echo $e;
//            $html .= "<div style=\"text-align:center\"><h3> mal ajoutee </h3> </div>";
                        }
                    }
                }
                $html .= "</form></center></ul>";
            }
        }
        return $html;

    }

    /**
     * @param \PDO $db
     * @param $id_user
     * @param $id
     * @return mixed
     */
    public function pasEnFavori(\PDO $db, $id_user, $id)
    {
        $stmt = $db->prepare("SELECT count(*) from useraime where id_user=? and id_serie=?");
        $stmt->execute([$id_user, $id]);
        $data = $stmt->fetch();
        return $data[0] == 0;
    }
}