<?php

namespace iutnc\netvod\authentification;

use iutnc\netvod\exception\AuthException as AuthException;
use iutnc\netvod\bd\ConnectionFactory as ConnectionFactory;

class Auth
{


    public static function authenticate(string $email, string $hpassword): void
    {
        ConnectionFactory::setConfig('src/classes/bd/db.config.ini');
        $bdd = ConnectionFactory::makeConnection();

        $requete = $bdd->prepare("select passhash,id from user where email=?");
        $requete->bindParam(1, $email);
        $requete->execute();

        $resultat = $requete->fetch();

        if (password_verify($hpassword, $resultat[0])) {
            self::loadProfile($email);
        } else {
            throw new AuthException("email ou mot de passe invalide");
        }
    }


    public static function loadProfile(string $email) {
        $q = "select * from user where email = ?";
        ConnectionFactory::setConfig('src/classes/bd/db.config.ini');
        $db = ConnectionFactory::makeConnection();

        $stmt = $db->prepare($q);
        $res = $stmt->execute([$email]);
        if (!$res) throw new AuthException("auth error sur query");

        $userDB = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$userDB) throw new AuthException("profile not found");
        $_SESSION['id_user'] = $userDB['id'];
    }

    public static function checkPasswordStrength(string $pass, int $minimumLength):bool{
        $length = (strlen($pass) > $minimumLength);
        $digit = preg_match("#[\d]#", $pass);
        $special = preg_match("#[\W]#", $pass);
        $lower = preg_match("#[a-z]#", $pass);
        $upper = preg_match("#[A-Z]#", $pass);

        return $length && $digit && $special && $lower && $upper;
    }


    public static function verifierCompteExistePas(string $email): void {
        try {
            ConnectionFactory::setConfig('src/classes/bd/db.config.ini');
            $db = ConnectionFactory::makeConnection();

            $query_email = "select * from user where email = ?";
            $stmt = $db->prepare($query_email);
            $stmt->execute([$email]);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            while ($data = $stmt->fetch()) {
                if ($data['email'] == $email) throw new AuthException("Compte déjà existant");
            }
        } catch (\PDOException $e) {
            throw new AuthException("erreur de creation de compte :".$e->getMessage());
        }
    }

    public static function checkCredentials(string $email, string $pass, string $pass_confirm): bool
    {
        if (!self::checkPasswordStrength($pass, 10)) throw new AuthException("mot de passe trop faible");

        $hash = password_hash($pass, PASSWORD_DEFAULT, ['cost' => 12]);
        if (!(password_verify($pass, $hash) && password_verify($pass_confirm, $hash))) {
            throw new AuthException("mots de passes non identiques");
        }
        return true;
    }

    public static function register(string $email, string $passhash) : void {
        try {
            ConnectionFactory::setConfig('src/classes/bd/db.config.ini');
            $db = ConnectionFactory::makeConnection();

            self::verifierCompteExistePas($email);

            $query = "insert into user (email, passhash) values(?,?)";
            $stmt = $db->prepare($query);
            $stmt->execute([$email, $passhash]);
        } catch (\PDOException $e) {
            throw new AuthException("erreur de creation de compte :".$e->getMessage());
        }
    }

    public static function verifierCompteExiste(string $email): void {
        try {
            ConnectionFactory::setConfig('src/classes/bd/db.config.ini');
            $db = ConnectionFactory::makeConnection();

            $query_email = "select * from user where email = ?";
            $stmt = $db->prepare($query_email);
            $stmt->execute([$email]);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $trouve=false;
            while ($data = $stmt->fetch()) {
                if ($data['email'] == $email){
                    $trouve = true;
                }
            }
            if (!$trouve) throw new AuthException("Compte inexistant");
        } catch (\PDOException $e) {
            throw new AuthException($e->getMessage());
        }
    }

    public static function changerMotDePasse(string $email, string $pass, string $pass_confirm): void {
        try {
            self::checkCredentials($email, $pass, $pass_confirm);
            ConnectionFactory::setConfig('src/classes/bd/db.config.ini');
            $db = ConnectionFactory::makeConnection();

            $passhash = password_hash($pass, PASSWORD_DEFAULT, ['cost' => 12]);
            $query_update = "update user set passhash = ? where email = ?";
            $stmt = $db->prepare($query_update);
            $stmt->execute([$passhash,$email]);

        } catch (\PDOException $e) {
            throw new AuthException("erreur du changement de mot de passe :".$e->getMessage());
        }
    }
}