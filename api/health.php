<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$ok = true; $msg = 'ok';
try {
  $pdo = DB::conn();
  $pdo->query("SELECT 1");
} catch (Throwable $e) {
  $ok = false; $msg = $e->getMessage();
}
echo json_encode([
  'env' => [
    'DB_HOST' => env('DB_HOST', null),
    'DB_NAME' => env('DB_NAME', null),
    'DB_USER' => env('DB_USER', null),
    'DB_PORT' => env('DB_PORT', null),
  ],
  'db_ok' => $ok,
  'message' => $msg
], JSON_UNESCAPED_SLASHES);
