<?php
require_once __DIR__ . '/../lib/Response.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../../config/db.php';

class SellerController {

    // GET /api/seller/orders  -> list order-items the seller must fulfill
    public static function myOrderItems() {
        $u = AuthMiddleware::requireAuth('shopowner');

        $pdo = DB::conn();
        $stmt = $pdo->prepare("
            SELECT oi.id AS item_id, oi.order_id, oi.product_id, oi.quantity, oi.price, oi.status, oi.status_updated_at,
                   p.name AS product_name,
                   o.user_id AS customer_id, o.created_at AS order_created_at
            FROM order_items oi
            JOIN orders o   ON o.id = oi.order_id
            JOIN products p ON p.id = oi.product_id
            WHERE oi.owner_id = ?
            ORDER BY oi.status='pending' DESC, o.created_at DESC
        ");
        $stmt->execute([$u['id']]);
        $rows = $stmt->fetchAll();
        Response::json(['data'=>$rows]);
    }

    // POST /api/seller/order-items/{id}/status -> {status: 'packed' | 'dispatched'}
    public static function updateItemStatus($item_id) {
        $u = AuthMiddleware::requireAuth('shopowner');
        $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        $status = $payload['status'] ?? '';

        if (!in_array($status, ['packed','dispatched','delivered'], true)) {
            Response::json(['error'=>'Invalid status'], 422); return;
        }

        $pdo = DB::conn();
        // verify ownership and get customer_id
        $stmt = $pdo->prepare("
            SELECT oi.*, o.user_id AS customer_id, p.name AS product_name
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id
            JOIN products p ON p.id = oi.product_id
            WHERE oi.id=? AND oi.owner_id=?
        ");
        $stmt->execute([(int)$item_id, (int)$u['id']]);
        $it = $stmt->fetch();
        if (!$it) { Response::json(['error'=>'Not found'],404); return; }

        // update status
        $stmt = $pdo->prepare("UPDATE order_items SET status=?, status_updated_at=NOW() WHERE id=?");
        $stmt->execute([$status, (int)$item_id]);

        // optional: add order_event
        $pdo->prepare("INSERT INTO order_events (order_id, actor_id, event, note) VALUES (?,?,?,?)")
            ->execute([$it['order_id'], $u['id'], $status, 'Line item updated']);

        // send notification to customer
        $title = $status==='packed' ? 'Item packed' :
                 ($status==='dispatched' ? 'Item dispatched' : 'Item delivered');
        $msg   = $status==='dispatched'
            ? "Your item '{$it['product_name']}' has been dispatched and will be delivered within 2 working days."
            : "Your item '{$it['product_name']}' was {$status}.";

        Notification::create(
            $it['customer_id'],
            'fulfilment',
            $title,
            $msg,
            ['order_id'=>$it['order_id'], 'item_id'=>$it['id'], 'status'=>$status]
        );

        Response::json(['ok'=>true, 'status'=>$status]);
    }
}
