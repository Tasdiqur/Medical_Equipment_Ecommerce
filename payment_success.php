<?php
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $cartData = json_decode($_POST['cartData'], true);

    if ($amount <= 0 || empty($cartData)) {
        echo "<h2>❌ Invalid payment request</h2>";
        exit;
    }

    try {
        $pdo = DB::conn();
        $pdo->beginTransaction();

        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?,?,?,NOW())");
        $userId = 1; // TODO: replace with actual logged-in customer id from session/token
        $stmt->execute([$userId, $amount, 'paid']);
        $orderId = $pdo->lastInsertId();

        // Insert order items
        foreach ($cartData as $item) {
            $productId = intval($item['product_id']);
            $qty = intval($item['quantity']);

            $stmt = $pdo->prepare("SELECT price FROM products WHERE id=?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();

            if ($product) {
                $lineTotal = $product['price'] * $qty;
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?,?,?,?)");
                $stmt->execute([$orderId, $productId, $qty, $product['price']]);
            }
        }

        $pdo->commit();

        echo "<!doctype html><html><head>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head><body class='bg-dark text-light container my-5'>
        <h2>✅ Payment Successful (Simulated)</h2>
        <p>Order ID: <strong>#{$orderId}</strong></p>
        <p>Amount Paid: <strong>৳" . number_format($amount, 2) . "</strong></p>
        <a href='index.php' class='btn btn-info mt-3'>Back to Shop</a>
        </body></html>";

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo "<h2>❌ Payment Error</h2>";
        echo "<pre>" . $e->getMessage() . "</pre>";
    }
} else {
    echo "<h2>❌ Invalid Access</h2>";
}
