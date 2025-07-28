<?php
require_once __DIR__ . '/../lib/Response.php';
require_once __DIR__ . '/../models/Order.php';

class OrderController {
    public static function create($user) {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['items']) || !is_array($data['items']) || count($data['items']) === 0) {
            Response::json(['error'=>'No items'], 422);
        }
        try {
            $order = Order::create($user['id'], $data['items']);
            Response::json(['data'=>$order], 201);
        } catch (Exception $e) {
            Response::json(['error'=>$e->getMessage()], 400);
        }
    }
    public static function list($user) {
        Response::json(['data'=>Order::listForUser($user['id'])]);
    }
}
