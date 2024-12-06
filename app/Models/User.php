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

    public static function verifyLogin($email, $password)
    {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR); // Use o namespace global \PDO
        $stmt->execute();
        
        $user = $stmt->fetch(\PDO::FETCH_ASSOC); // Use \PDO aqui também

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        return $user;
    }

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
        $stmt = $db->prepare('INSERT INTO users (username, email, password_hash, user_created_by) VALUES (:username, :email, :password_hash, "Sign Up")');
        $stmt->bindValue(':username', $username, \PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $passwordHash, \PDO::PARAM_STR);
        return $stmt->execute();
    }
    public static function generateRandomPasswordHash()
    {
        return password_hash(uniqid(rand(), true), PASSWORD_DEFAULT);
    }
    public static function createSocialUser($username, $email)
    {
        $passwordHash = self::generateRandomPasswordHash(); 
        $db = self::getDB();

        $stmt = $db->prepare('INSERT INTO users (username, email, password_hash, is_verified, user_created_by) VALUES (:username, :email, :password_hash, 1, "Social User")');
        $stmt->bindValue(':username', $username, \PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $passwordHash, \PDO::PARAM_STR);

        return $stmt->execute();
    }
}
