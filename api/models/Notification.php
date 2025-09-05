<?php
require_once __DIR__ . '/../../config/db.php';

class Notification {
    public static function create($user_id, $type, $title, $message, $meta = null) {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, meta) VALUES (?,?,?,?,?)");
        $stmt->execute([$user_id, $type, $title, $message, $meta ? json_encode($meta) : null]);
        return $pdo->lastInsertId();
    }

    public static function listFor($user_id, $only_unread=false, $limit=20, $offset=0) {
        $pdo = DB::conn();
        $sql = "SELECT * FROM notifications WHERE user_id=? ";
        if ($only_unread) $sql .= "AND is_read=0 ";
        $sql .= "ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, (int)$user_id, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function markRead($id, $user_id) {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?");
        return $stmt->execute([$id, $user_id]);
    }
}
