<?php
require_once __DIR__ . '/../lib/Response.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class ProductController
{
    // GET /api/products?limit=&offset=
    public static function list() {
        $limit  = isset($_GET['limit'])  ? max(1, min(100, (int)$_GET['limit']))   : 24;
        $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset'])            : 0;

        $pdo = DB::conn();
        $stmt = $pdo->prepare("
            SELECT p.id, p.owner_id, p.name, p.description, p.price, p.stock, p.category, p.image_url,
                   p.created_at, p.updated_at,
                   u.full_name AS owner_name
            FROM products p
            LEFT JOIN users u ON u.id = p.owner_id
            ORDER BY p.created_at DESC, p.id DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return Response::json(['data' => $rows], 200);
    }

    // GET /api/products/{id}
    public static function get($id) {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("
            SELECT p.id, p.owner_id, p.name, p.description, p.price, p.stock, p.category, p.image_url,
                   p.created_at, p.updated_at,
                   u.full_name AS owner_name
            FROM products p
            LEFT JOIN users u ON u.id = p.owner_id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return Response::json(['error' => 'Not found'], 404);
        return Response::json(['data' => $row], 200);
    }

    // GET /api/products/mine (shopowner)
    public static function byOwner($user) {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("
            SELECT id, owner_id, name, description, price, stock, category, image_url, created_at, updated_at
            FROM products
            WHERE owner_id = ?
            ORDER BY created_at DESC, id DESC
        ");
        $stmt->execute([$user['id']]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return Response::json(['data' => $rows], 200);
    }

    // POST /api/products (shopowner)
    public static function create($user) {
        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $name = trim($input['name'] ?? '');
        $desc = trim($input['description'] ?? '');
        $price = (float)($input['price'] ?? 0);
        $stock = (int)($input['stock'] ?? 0);
        $cat = trim($input['category'] ?? '');
        $img = trim($input['image_url'] ?? '');

        if ($name === '' || $price < 0) {
            return Response::json(['error' => 'Name and valid price are required'], 422);
        }

        $pdo = DB::conn();
        $stmt = $pdo->prepare("
            INSERT INTO products (owner_id, name, description, price, stock, category, image_url)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$user['id'], $name, $desc, $price, $stock, $cat, $img]);
        $id = (int)$pdo->lastInsertId();

        return self::get($id);
    }

    // PUT /api/products/{id} (shopowner)
    public static function update($user, $id) {
        // verify ownership
        $pdo = DB::conn();
        $own = $pdo->prepare("SELECT owner_id FROM products WHERE id = ?");
        $own->execute([$id]);
        $row = $own->fetch(PDO::FETCH_ASSOC);
        if (!$row) return Response::json(['error' => 'Not found'], 404);
        if ((int)$row['owner_id'] !== (int)$user['id']) return Response::json(['error' => 'Forbidden'], 403);

        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $name = trim($input['name'] ?? '');
        $desc = trim($input['description'] ?? '');
        $price = (float)($input['price'] ?? 0);
        $stock = (int)($input['stock'] ?? 0);
        $cat = trim($input['category'] ?? '');
        $img = trim($input['image_url'] ?? '');

        $stmt = $pdo->prepare("
            UPDATE products
            SET name=?, description=?, price=?, stock=?, category=?, image_url=?, updated_at=NOW()
            WHERE id=?
        ");
        $stmt->execute([$name, $desc, $price, $stock, $cat, $img, $id]);

        return self::get($id);
    }

    // DELETE /api/products/{id} (shopowner)
    public static function delete($user, $id) {
        // verify ownership
        $pdo = DB::conn();
        $own = $pdo->prepare("SELECT owner_id FROM products WHERE id = ?");
        $own->execute([$id]);
        $row = $own->fetch(PDO::FETCH_ASSOC);
        if (!$row) return Response::json(['error' => 'Not found'], 404);
        if ((int)$row['owner_id'] !== (int)$user['id']) return Response::json(['error' => 'Forbidden'], 403);

        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return Response::json(['data' => ['deleted' => true]], 200);
    }
}
