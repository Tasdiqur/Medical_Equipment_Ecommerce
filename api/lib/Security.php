<?php
require_once __DIR__ . '/../../config/db.php';

class Security {
    public static function sanitize($str) {
        return trim(filter_var($str, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
    }
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    public static function token() {
        return bin2hex(random_bytes(32));
    }
    public static function ttlExpires() {
        $hours = (int) env('TOKEN_TTL_HOURS', 72);
        return date('Y-m-d H:i:s', time() + $hours * 3600);
    }
}
