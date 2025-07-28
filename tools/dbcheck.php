<?php
// medrex-php/tools/dbcheck.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/db.php';   // loads env + PDO

try {
    $pdo = DB::conn();
    echo "<pre>Connected OK!\n";
    // Show current DB and a quick query
    $db = getenv('DB_NAME') ?: 'medrex';
    echo "DB_NAME from env: " . htmlspecialchars($db) . "\n";
    $stmt = $pdo->query("SHOW TABLES");
    echo "Tables:\n";
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $t) {
        echo " - $t\n";
    }
    echo "</pre>";
} catch (Throwable $e) {
    echo "<pre>DB ERROR:\n" . $e->getMessage() . "\n</pre>";
}
