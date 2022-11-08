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

            if ($this->http_method === "GET") {
                if (!$commentaire) {
                    $html .=
                        " <form id=\"f1\" method=\"post\" action='?action=episode&id_episode=" . $_GET['id_episode'] . "'>
                        <div style=\"text-align: center\"> 
                        <input type=\"number\" placeholder=\"De 1 à 5\" name=\"note\"> </div>
                        <div style=\"text-align: center\"> 
                        <input type=\"text\" placeholder=\"Ecrivez votre commentaire\" name=\"comm\"> </div>
                        <div style=\"text-align: center\"> 
                        <button type=\"submit\" name=\"commentaire\" value=\"vrai\"> Valider </button> </div>
                        </form>";
                } else {
                    $html .= "<div style=\"text-align:center\"><h3> Vous avez déja laissé un commentaire à cette série </h3> </div>";
                }
            } else {
                if (isset($_POST['note'])&&isset($_POST['comm'])) {

                } else {
                    $html .= "<div style=\"text-align:center\"><h3> Veuillez compléter la note par un entier entre 1 et 5
                    et l'espace commentaire </h3> </div> <br>";
                }
            }

        }
        return $html;
    }

    public function htmlComm() : string {
        return " <form id=\"f1\" method=\"post\" action='?action=episode&id_episode=" . $_GET['id_episode'] . "'>
                        <div style=\"text-align: center\"> 
                        <input type=\"number\" placeholder=\"De 1 à 5\" name=\"note\"> </div>
                        <div style=\"text-align: center\"> 
                        <input type=\"text\" placeholder=\"Ecrivez votre commentaire\" name=\"comm\"> </div>
                        <div style=\"text-align: center\"> 
                        <button type=\"submit\" name=\"commentaire\" value=\"vrai\"> Valider </button> </div>
                        </form>";
    }
}