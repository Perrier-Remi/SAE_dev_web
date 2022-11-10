<?php

namespace iutnc\netvod\action;

require_once 'src/classes/NetVOD/Episode.php';
require_once 'src/classes/bd/ConnectionFactory.php';

use iutnc\netvod\bd\ConnectionFactory;
use iutnc\netvod\NetVOD\Episode;
use iutnc\netvod\render\RenderEpisode;

class ActionEpisode extends Action
{

    public function execute(): string
    {
        $html = "";
        if (!isset($_GET['id_episode'])) {
            $html .= "<div style=\"text-align:center\"><h3> Id Episode Manquant </h3> </div>";
        } elseif (!isset($_SESSION['user'])) {
            $html .= "<p> Utilisateur Non Connecté </p>";
        } else {
            try {
                $db = ConnectionFactory::makeConnection();
            } catch (\Exception $e) {
                $html .= "<p> Connection à la base de données impossible</p>";
            }

            $serie_stmt = $db->prepare("SELECT serie_id FROM episode where id=?");
            try {
                $serie_stmt->execute([$_GET['id_episode']]);
            } catch (\Exception $e) {
                $html .= "<div style=\"text-align:center\"><h3> Episode inconnu </h3> </div>";
            }
            $id_serie = $serie_stmt->fetch()[0];
            $id_user = unserialize($_SESSION['user'])->__get('id');
            $stmt_encours = $db->prepare("INSERT INTO serieEnCours(id_user, id_serie) VALUES (?,?)");
            try {
                $stmt_encours->execute([$id_user, $id_serie]);
                $html .= "<div style=\"text-align:center\"><h3> Nouvelle série </h3> </div>";
            } catch (\Exception $e) {
                $html .= "<div style=\"text-align:center\"><h3> Série en cours </h3> </div>";
            }


            $stmt = $db->prepare("SELECT * FROM episode WHERE id = ?");
            try {
                $stmt->execute([$_GET['id_episode']]);
            } catch (\Exception $e) {
                $html .= "";
            }
            $donnees = $stmt->fetch();
            $episode = new Episode($donnees[1], $donnees[2], $donnees[4], $donnees[3], $donnees[5]);
            $renderer = new RenderEpisode($episode);
            $html .= $renderer->render();

            // Vérification si l'utilisateur a déjà laissé un commentaire pour cette série
            $stmt_comm = $db->prepare("SELECT COUNT(*) FROM commentaires WHERE id_user = ? AND id_serie = ?");
            $stmt_comm->execute([$id_user, $id_serie]);
            $nb_comm_serie = $stmt_comm->fetch()[0];
            if ($nb_comm_serie === 0) {
                $commentaire = false;
            } else {
                $commentaire = true;
            }

            if ($commentaire) {
                $html .= "<div style=\"text-align:center\"><h3> Vous avez déjà posté un commentaire pour cette série </h3> </div> <br>";
            } else {
                if (isset($_POST['note']) && isset($_POST['comm'])) {
                    $comm = filter_var($_POST['comm'], FILTER_SANITIZE_STRING);
                    if ($_POST['comm'] == null || $comm == "") {
                        $html .= "<div style=\"text-align:center\"><h3> Veuillez écrire un commentaire en mettant une note ! </h3> </div> <br>";
                        $html .= $this->htmlComm();
                    } else {
                        $stmt_addComm = $db->prepare("INSERT INTO commentaires VALUES (?,?,?,?);");
                        try {
                            $stmt_addComm->execute([$id_user, $id_serie, $_POST['note'], $comm]);
                            $html .= "<div style=\"text-align:center\"><h3> Commentaire ajouté ! </h3> </div> <br>";
                        } catch (\Exception $e) {
                            $html .= "<div style=\"text-align:center\"><h3> Erreur dans l'ajout du commentaire </h3> </div> <br>";
                        }
                    }
                } else {
                    $html .= $this->htmlComm();
                }
            }

        }
        return $html;
    }

    public function htmlComm(): string
    {
        return " <form id=\"f1\" method=\"post\" action='?action=episode&id_episode=" . $_GET['id_episode'] . "'>
                        <div style=\"text-align: center\"> 
                        <label>Note de 1 à 5 : </label>
                        <input type=\"number\" min=\"1\" max=\"5\" step=\"1\" name=\"note\"> </div>
                        <div style=\"text-align: center\"> <label> Commentaire : </label> </div>
                        <div style=\"text-align: center\">
                        <textarea type=\"text\" name=\"comm\" rows='8' cols='55'></textarea> </div>
                        <div style=\"text-align: center\"> 
                        <button type=\"submit\" name=\"commentaire\" value=\"vrai\"> Valider </button> </div>
                        </form>";
    } // changer notation
}