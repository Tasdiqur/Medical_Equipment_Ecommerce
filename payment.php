<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Payment - MedRex</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">
  <script src="assets/js/app.js"></script>
  <style>
    body, .card, p, h5, h2 { color: #fff !important; }
  </style>
</head>
<body class="bg-dark text-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-black sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Med<span class="text-info">REX</span></a>
  </div>
</nav>

<main class="container my-5">
  <h2 class="mb-4">Payment Options</h2>

  <div class="card bg-black border border-secondary p-4">
    <h5 class="mb-3">Simulated Payment</h5>
    <p id="totalAmount">Total Amount: <strong>৳0.00</strong></p>
    <p>(This will just confirm order without real bKash integration.)</p>
    <form method="post" action="payment_success.php" id="payForm">
      <input type="hidden" name="amount" id="amountField">
      <input type="hidden" name="cartData" id="cartData">
      <button type="submit" class="btn btn-info">Confirm Payment</button>
    </form>
  </div>
</main>

<script>
function calcCartTotal() {
  const raw = localStorage.getItem('cart') || '[]';
  console.log("Cart raw data:", raw);   // 👈 debug
  const cart = JSON.parse(raw);
  let total = 0;

  cart.forEach(item => {
    let price = parseFloat(item.price);
    if (isNaN(price)) price = 0;

    let qty = parseInt(item.quantity);
    if (isNaN(qty)) qty = 0;

    console.log("Item:", item, "Price:", price, "Qty:", qty); // 👈 debug
    total += price * qty;
  });

  document.getElementById('totalAmount').innerHTML =
    "Total Amount: <strong>৳" + total.toFixed(2) + "</strong>";

  document.getElementById('amountField').value = total;
  return cart;
}


document.addEventListener("DOMContentLoaded", () => {
  const cart = calcCartTotal();

  document.getElementById('payForm').addEventListener('submit', function() {
    document.getElementById('cartData').value = JSON.stringify(cart);
    // clear cart after submission
    localStorage.removeItem('cart');
    updateCartCount();
  });
});
</script>

</body>
</html>
