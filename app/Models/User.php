<?php
namespace App\Models;

use Core\Model;

class User extends Model {

        public static function findByVerificationToken($token)
    {
        $db = self::getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE verification_token = :token');
        $stmt->bindValue(':token', $token, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Marcar o e-mail como verificado
    public static function verifyEmail($email)
    {
        $db = self::getDB();
        $stmt = $db->prepare('UPDATE users SET is_verified = 1 WHERE email = :email');
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    public static function storeVerificationToken($email, $token)
    {
        $db = self::getDB();
        $stmt = $db->prepare("UPDATE users SET verification_token = :token WHERE email = :email");
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->bindValue(':token', $token, \PDO::PARAM_STR);
        return $stmt->execute();
    }


    // Método para verificar o token de verificação
    public static function findByToken($token)
    {
        $db = self::getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE verification_token = :token');
        $stmt->bindValue(':token', $token, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function findByEmail($email) {
        $db = self::getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function updateVerificationStatus($email) {
        $db = self::getDB();
        $stmt = $db->prepare("UPDATE users SET is_verified = 1 WHERE email = :email");
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        return $stmt->execute();
    }
    public static function create($username, $email, $passwordHash) {
        $db = self::getDB();
        $stmt = $db->prepare('INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)');
        $stmt->bindValue(':username', $username, \PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $passwordHash, \PDO::PARAM_STR);
        return $stmt->execute();
    }
}
