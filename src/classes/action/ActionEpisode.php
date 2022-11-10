<?php

namespace iutnc\netvod\action;

use iutnc\netvod\bd\ConnectionFactory;
use iutnc\netvod\NetVOD\Episode;
use iutnc\netvod\NetVOD\Serie;
use iutnc\netvod\render\RenderEpisode;

class ActionEpisode extends Action
{

    public function execute(): string
    {
        $html = "";
        if (!isset($_GET['id_episode'])) {
            $html .= "<div style=\"text-align:center\"><h3> Id Episode Manquant </h3> </div>";
        } elseif (!isset($_SESSION['id_user'])) {
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
            $id_user = $_SESSION['id_user'];

            $html .= $this->serieEnCours($db, $id_user, $id_serie);
            $html .= $this->episodeEnCours($db , $id_user, $id_serie);


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
                            Serie::getNoteMoyenne($id_serie);
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

    public function serieEnCours(\PDO $db, $id_user, $id_serie) : string {
        $html = "";
        $stmt_encours = $db->prepare("INSERT INTO serieEnCours(id_user, id_serie) VALUES (?,?)");
        if (!$this->serieDejaVu($db, $id_user, $id_serie)) {
            try {
                $stmt_encours->execute([$id_user, $id_serie]);
                $html .= "<div style=\"text-align:center\"><h3> Nouvelle série </h3> </div>";
            } catch (\Exception $e) {
                $html .= "<div style=\"text-align:center\"><h3> Série en cours </h3> </div>";
            }
        } else {
            $html .= "<div style=\"text-align:center\"><h3> Série finie ! </h3> </div>";
        }
        return $html;
    }

    public function episodeEnCours(\PDO $db, $id_user, $id_serie) : string {
        $html = "";
        $stmt_episodeInListe = $db->prepare("SELECT COUNT(*) FROM episodeEnCours WHERE id_episode = ?");
        $stmt_episodeInListe->execute([$_GET['id_episode']]);
        // Si l'episode n'a jamais été regardé
        if ($stmt_episodeInListe->fetch()[0] === 0) {
            $stmt_episode = $db->prepare("INSERT INTO episodeEnCours(id_user, id_serie, id_episode, actuel) VALUES (?,?,?,true)");
            try {
                $stmt_episode->execute([$id_user, $id_serie, $_GET['id_episode']]);
                $html .= "<div style=\"text-align:center\"><h3> Nouvel épisode </h3> </div>";
            } catch (\Exception $e) {
                $html .= "<div style=\"text-align:center\"><h3> Erreur dans la requête SQL epEnCours </h3> </div>";
            }
        // Si il a déjà été regardé
        } else {
            $stmt_episode = $db->prepare("UPDATE episodeEnCours SET actuel = true WHERE id_episode = ?");
            try {
                $stmt_episode->execute([$_GET['id_episode']]);
                $html .= "<div style=\"text-align:center\"><h3> Revisionnage de l'épisode </h3> </div>";
            } catch (\Exception $e) {
                $html .= "<div style=\"text-align:center\"><h3> Erreur dans la requête SQL epEnCours </h3> </div>";
            }
        }

        // Enlever l'ancien épisode courant si il existe
        $stmt_oldEp = $db->prepare("SELECT id_episode FROM episodeEnCours WHERE actuel = true AND id_episode != ?");
        try {
            $stmt_oldEp->execute([$_GET['id_episode']]);
            if (isset($stmt_oldEp->fetch(\PDO::FETCH_ASSOC)[0])) {
                $oldEp = $stmt_oldEp->fetch()[0];
                $stmt_updateOldEp = $db->prepare("UPDATE episodeEnCours SET actuel = false WHERE id_episode = ?");
                $stmt_updateOldEp->execute([$oldEp]);
            }
        } catch (\Exception $e) {
            $html .= "<div style=\"text-align:center\"><h3> Erreur dans la requête SQL epEnCours</h3> </div>";
        }
        return $html;
    }

    public function serieDejaVu(\PDO $db, $id_user, $id_serie) : bool {
        $serieCompletee = false;
        // Nombre total d'épisodes de la série
        $stmt_nbEpSerie = $db->prepare("SELECT COUNT(*) FROM episode WHERE serie_id = ?");
        $stmt_nbEpSerie->execute([$id_serie]);
        $nbEpSerie = $stmt_nbEpSerie->fetch()[0];
        // Nombre d'épisodes de la série déjà vus
        $stmt_nbEpVus = $db->prepare("SELECT COUNT(*) FROM episodeEnCours WHERE id_serie = ?");
        $stmt_nbEpVus->execute([$id_serie]);
        $nbEpVus = $stmt_nbEpVus->fetch()[0];

        // Si la série est complétée et tous les épisodes visionnés, la mettre dans la table serieDejaVisionnee
        // Vérifier si la série est déjà comptée comme complétée
        $stmt_verify = $db->prepare("SELECT COUNT(*) FROM serieDejaVisionnee WHERE id_user = ? AND id_serie = ?");
        $stmt_verify->execute([$id_user, $id_serie]);
        $serieIn = $stmt_verify->fetch()[0];

        if ($serieIn === 0) {
            if ($nbEpSerie === $nbEpVus) {
                $stmt_add = $db->prepare("INSERT INTO serieDejaVisionnee(id_user, id_serie) VALUES (?,?)");
                $stmt_delete = $db->prepare("DELETE FROM serieEnCours WHERE id_user = ? AND id_serie = ?");
                $stmt_add->execute([$id_user, $id_serie]);
                $stmt_delete->execute([$id_user, $id_serie]);
                $serieCompletee = true;
            }
        } else {
            $serieCompletee = true;
        }

        return $serieCompletee;
    }
}