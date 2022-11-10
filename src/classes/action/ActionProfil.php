<?php

namespace iutnc\netvod\action;

use iutnc\netvod\bd\ConnectionFactory;

class ActionProfil extends Action
{

    public function execute(): string
    {
        $html = "";
        if (isset($_SESSION['id_user'])) {

            try {
                $db = ConnectionFactory::makeConnection();
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $html .= "<p> Connection à la base de données impossible</p>";
            }

            if (isset($_POST['save'])) {
                $new_nom = filter_var($_POST['nom'], FILTER_SANITIZE_STRING);
                $new_prenom = filter_var($_POST['prenom'], FILTER_SANITIZE_STRING);
                $new_genre = $_POST['genre'];
                if ($new_nom == null) $new_nom = "Aucun";
                if ($new_prenom == null) $new_prenom = "Aucun";
                if ($new_genre == null) $new_genre = "Non renseigné";

                // Crée un string avec la liste des genres préférés
                $action = null;
                $aventure = null;
                $thriller = null;
                $horreur = null;
                $romance = null;
                if (isset($_POST['action'])) $action = $_POST['action'];
                if (isset($_POST['aventure'])) $aventure = $_POST['aventure'];
                if (isset($_POST['thriller'])) $thriller = $_POST['thriller'];
                if (isset($_POST['horreur'])) $horreur = $_POST['horreur'];
                if (isset($_POST['romance'])) $romance = $_POST['romance'];
                $listeGenre = [$action, $aventure, $thriller, $horreur, $romance];
                $new_GenresPrefs = "";
                foreach ($listeGenre as $value) {
                    if ($value != null) {
                        $new_GenresPrefs .= $value . ",";
                    }
                }
                $new_GenresPrefs = rtrim($new_GenresPrefs, ",");

                // Sauvegarde les paramètres rentrés
                $stmt_newInfo = $db->prepare("UPDATE profils SET nom = ?, prenom = ?, genre = ?, genresPref = ? WHERE id_user = ?;");
                try {
                    $stmt_newInfo->execute([$new_nom, $new_prenom, $new_genre, $new_GenresPrefs, $_SESSION['id_user']]);
                } catch (\Exception $e) {
                    $html .= "<div style=\"text-align:center\"><h3> Erreur dans la requête SQL </h3> </div> <br>";
                }

            } elseif (isset($_POST['notsave'])) {
                $html .= "<div style=\"text-align:center\"><h3> Les changements n'ont pas été appliqués </h3> </div> <br>";
            }

            $html .= $this->profilHtml();


        }
        return $html;
    }

    public function profilHtml() : string {

        return " <form method=\"post\" action='?action=profil'>
                        <br> <br> <br>
                        <div style=\"text-align: center\"> 
                        <label> Nom : </label>
                        <input type=\"text\" size='50' placeholder='Nouveau nom' name=\"nom\"> </div> <br> <br>
                        <div style=\"text-align: center\"> 
                        <label> Prénom : </label>
                        <input type=\"text\" size='50' placeholder='Nouveau prenom' name=\"prenom\"> </div> <br> <br>
                        <div style=\"text-align: center\">
                            <input type='radio' id='h' name='genre' value='Homme' checked>
                            <label for='h'>Homme</label>
                            <input type='radio' id='f' name='genre' value='Femme'>
                            <label for='f'>Femme</label>
                        </div> <br> <br>
                        
                        <div style=\"text-align: center\">
                            <label id='descprofilgnr'><br>Choisissez votre genre de séries préféré :<br><br></label>
                            <input type='checkbox' id='action' name='action' value='action'> <label for='action'> Action</label>
                            <input type='checkbox' id='aventure' name='aventure' value='aventure'> <label for='aventure'> Aventure</label>
                            <input type='checkbox' id='thriller' name='thriller' value='thriller'> <label for='thriller'> Thriller</label>
                            <input type='checkbox' id='horreur' name='horreur' value='horreur'> <label for='horreur'> Horreur </label>
                            <input type='checkbox' id='romance' name='romance' value='romance'> <label for='romance'> Romance </label>
                       </div> <br> <br>
                       <div style=\"text-align: center\">
                            <button type=\"submit\" name=\"save\" value=\"vrai\"> Sauvegarder </button>
                            <button type=\"submit\" name=\"notsave\" value=\"vrai\"> Ne pas sauvegarder </button> 
                       </div> <br> <br>
                        </form>";
    }
}