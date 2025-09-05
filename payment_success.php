<?php
// Simple success page. The order is already created via API from payment.php.
$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$total   = isset($_GET['total']) ? htmlspecialchars($_GET['total']) : '0.00';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Payment Success - MedRex</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">
  <style> body, .card, p, h5, h2 { color:#fff !important; } </style>
</head>
<body class="bg-dark text-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-black sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Med<span class="text-info">REX</span></a>
  </div>
</nav>

<main class="container my-5">
  <div class="card bg-black border border-secondary p-4">
    <h2 class="mb-3">✅ Payment Successful</h2>
    <p class="mb-1">Order ID: <strong>#<?php echo $orderId; ?></strong></p>
    <p class="mb-4">Amount Paid: <strong>৳<?php echo $total; ?></strong></p>
    <a href="index.php" class="btn btn-info">Back to Shop</a>
  </div>
</main>

</body>
</html>
