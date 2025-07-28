<?php
require_once __DIR__ . '/config.php';

class DB {
    private static $pdo = null;

    public static function conn() {
        if (self::$pdo === null) {
            $host = env('DB_HOST', '127.0.0.1');
            $db   = env('DB_NAME', 'medrex');
            $user = env('DB_USER', 'root');
            $pass = env('DB_PASS', '');
            $port = env('DB_PORT', '3306');
            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
            try {
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Database connection failed', 'details' => $e->getMessage()]);
                exit();
            }
        }
        return self::$pdo;
    }
}
