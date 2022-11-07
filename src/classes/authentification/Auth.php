<?php

namespace iutnc\netvod\authentification;

use SAE_dev_web\src\classes\bd\ConnectionFactory;
use SAE_dev_web\src\classes\authentification\User;
//use iutnc\deefy\exception\AuthException;

class Auth
{

    public static function authenticate(string $email, string $hpassword): mixed
    {
        ConnectionFactory::setConfig('src/classes/bd/db.config.ini');
        $bdd = ConnectionFactory::makeConnection();

        $requete = $bdd->prepare("select passhash from user where email=?");
        $requete->bindParam(1, $email);
        $requete->execute();

        $resultat = $requete->fetch()[0];

        if (password_verify($hpassword, $resultat)) {
            $retour = new User($email, $resultat);
            self::loadProfile($email);
        } else {
            $retour = null;
        }
        return $retour;
    }

    public static function loadProfile( string $email):void {
        $query="select * from user where email = ?";
        ConnectionFactory::setConfig('src/classes/bd/db.config.ini');
        $bd = ConnectionFactory::makeConnection();

        $stmt=$bd->prepare($query);
        $res = $stmt->execute([$email]);
        if (!$res) throw new AuthException("auth error : db query failed");

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) throw new AuthException("auth failed : profile not found");
        $user = new User($email, $user['passwd']);
        $_SESSION['user'] = serialize($user);
    }

    public static function checkAccessLevel (int $required): void
    {
        $userlevel = (int)(unserialize($_SESSION['user']))->role;
        if (!$userlevel >= $required) throw new AuthException("action non autorisee");
    }

    public static function checkAccessOwner(int $playlistId):void {
        if (! isset($_SESSION['user'])) throw new \Exception("action non authorisée : défaut d' authentification");
        $user = unserialize($_SESSION['user']);
        if ($user->role === User::ADMIN_USER) return;

        $query = 'SELECT * FROM user u , user2playlist p where email = ? and u.id = p.id_user and p.id_pl = ?';
        $db = ConnectionFactory::makeConnection();

        $stmt = $db->prepare($query);
        $res = $stmt->execute([$user->email,$playlistId]);
        if (!$res) throw new \Exception("error");

        if (!$stmt->fetch(\PDO::FETCH_ASSOC)) throw new \Exception("error");
    }

    public static function checkPasswordStrength(string $pass, int $minimumLength):bool{
        $length = (strlen($pass) > $minimumLength);
        $digit = true; //preg_match("#[\d]#", $pass);
        $special = true; //preg_match("#[\W]#", $pass);
        $lower = true; // preg_match("#[a-z]#", $pass);
        $upper = true; //preg_match("#[A-Z]#", $pass);

        if (!$length || !$digit || !$special || !$lower || !$upper) return false;
        return true;
    }

    public static function register(string $email, string $pass): bool{
        if (!self::checkPasswordStrength($pass,10)) throw new AuthException("password trop faible");
        $hash=password_hash($pass, PASSWORD_DEFAULT,['cost'=> 12] );
        try {
            $db= ConnectionFactory::makeConnection();
        }catch (\Exception $e){
            throw new AuthException($e ->getMessage());
        }
        $query_email = "select * from user where email = ?";
        $stmt = $db->prepare($query_email);
        $stmt->execute([$email]);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        while ($data=$stmt->fetch()){
            if ($data['email'] == $email) throw new AuthException("Compte déjà existant");
            echo $data['email'];
        }

        try {
            $query = "insert into user (email, passwd ) values(?,?)";
            $stmt = $db->prepare($query);
            $res = $stmt->execute([$email, $hash]);
        } catch (\PDOException $e) {
            throw new AuthException("erreur de creation de compte : :".$e->getMessage());
        }
        return true;
    }
}