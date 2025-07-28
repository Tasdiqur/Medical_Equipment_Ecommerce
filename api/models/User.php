<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../lib/Security.php';

class User {
    public static function create($full_name, $email, $password, $role) {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?,?,?,?)");
        $stmt->execute([$full_name, strtolower($email), Security::hashPassword($password), $role]);
        return self::findByEmail($email);
    }

    public static function findByEmail($email) {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("SELECT id, full_name, email, role, password_hash FROM users WHERE email=?");
        $stmt->execute([strtolower($email)]);
        return $stmt->fetch();
    }

    public static function createToken($user_id) {
        $pdo = DB::conn();
        $token = Security::token();
        $exp = Security::ttlExpires();
        $stmt = $pdo->prepare("INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?,?,?)");
        $stmt->execute([$user_id, $token, $exp]);
        return $token;
    }

    public static function getByToken($token) {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("SELECT u.id, u.full_name, u.email, u.role FROM user_tokens t JOIN users u ON u.id=t.user_id WHERE t.token=? AND t.expires_at > NOW()");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public static function deleteToken($token) {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("DELETE FROM user_tokens WHERE token=?");
        return $stmt->execute([$token]);
    }
}
