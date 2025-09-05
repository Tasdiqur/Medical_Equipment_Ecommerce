<?php
require_once __DIR__ . '/../lib/Response.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/Notification.php';

class NotificationController {
    public static function listMine() {
        $u = AuthMiddleware::requireAuth();
        $only_unread = isset($_GET['unread']) && $_GET['unread']=='1';
        $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $rows = Notification::listFor($u['id'], $only_unread, $limit, $offset);
        Response::json(['data'=>$rows]);
    }

    public static function markRead($id) {
        $u = AuthMiddleware::requireAuth();
        $ok = Notification::markRead($id, $u['id']);
        Response::json(['ok'=>$ok]);
    }
}
