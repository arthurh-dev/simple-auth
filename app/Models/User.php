<?php
namespace App\Models;

use Core\Model;

class User extends Model {
    public static function findByEmail($email) {
        $db = self::getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
