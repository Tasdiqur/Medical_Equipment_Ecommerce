<?php
require_once __DIR__ . '/../../config/db.php';

class Order {
    public static function create($user_id, $items) {
        $pdo = DB::conn();
        $pdo->beginTransaction();
        try {
            $total = 0;
            // Validate stock and compute total
            foreach ($items as $it) {
                $stmt = $pdo->prepare("SELECT price, stock FROM products WHERE id=? FOR UPDATE");
                $stmt->execute([$it['product_id']]);
                $p = $stmt->fetch();
                if (!$p) throw new Exception('Invalid product');
                if ((int)$p['stock'] < (int)$it['quantity']) throw new Exception('Insufficient stock');
                $total += (float)$p['price'] * (int)$it['quantity'];
            }
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?,?,?)");
            $stmt->execute([$user_id, $total, 'Processing']);
            $order_id = $pdo->lastInsertId();
            foreach ($items as $it) {
                $stmt = $pdo->prepare("SELECT price, stock FROM products WHERE id=?");
                $stmt->execute([$it['product_id']]);
                $p = $stmt->fetch();
                $price = (float)$p['price'];
                $q = (int)$it['quantity'];
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_each) VALUES (?,?,?,?)");
                $stmt->execute([$order_id, $it['product_id'], $q, $price]);
                // decrement stock
                $stmt = $pdo->prepare("UPDATE products SET stock=stock-? WHERE id=?");
                $stmt->execute([$q, $it['product_id']]);
            }
            $pdo->commit();
            return self::get($order_id);
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function listForUser($user_id) {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        $orders = $stmt->fetchAll();
        foreach ($orders as &$o) {
            $o['items'] = self::items($o['id']);
        }
        return $orders;
    }

    public static function get($id) {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id=?");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        if ($order) $order['items'] = self::items($id);
        return $order;
    }

    private static function items($order_id) {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE order_id=?");
        $stmt->execute([$order_id]);
        return $stmt->fetchAll();
    }
}
