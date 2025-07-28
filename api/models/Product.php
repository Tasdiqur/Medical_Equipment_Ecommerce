<?php
require_once __DIR__ . '/../../config/db.php';

class Product {
    public static function all($limit = 20, $offset = 0) {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("SELECT p.*, u.full_name AS owner_name FROM products p JOIN users u ON u.id=p.owner_id ORDER BY p.created_at DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function byOwner($owner_id) {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE owner_id=? ORDER BY created_at DESC");
        $stmt->execute([$owner_id]);
        return $stmt->fetchAll();
    }

    public static function get($id) {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($owner_id, $data) {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("INSERT INTO products (owner_id, name, description, price, stock, category, image_url) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([
            $owner_id,
            $data['name'], $data['description'], $data['price'],
            $data['stock'], $data['category'], $data['image_url'] ?? null
        ]);
        return self::get($pdo->lastInsertId());
    }

    public static function update($id, $owner_id, $data) {
        $pdo = DB::conn();
        $curr = self::get($id);
        if (!$curr || (int)$curr['owner_id'] !== (int)$owner_id) { return null; }
        $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, category=?, image_url=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([
            $data['name'], $data['description'], $data['price'],
            $data['stock'], $data['category'], $data['image_url'] ?? null, $id
        ]);
        return self::get($id);
    }

    public static function delete($id, $owner_id) {
        $pdo = DB::conn();
        $curr = self::get($id);
        if (!$curr || (int)$curr['owner_id'] !== (int)$owner_id) { return false; }
        $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
        return $stmt->execute([$id]);
    }
}
