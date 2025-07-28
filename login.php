<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MedRex — Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">
  <!-- cache-bust to ensure latest JS loads -->
  <script defer src="assets/js/app.js?v=10"></script>
</head>
<body class="bg-dark text-light d-flex align-items-center" style="min-height:100vh">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card bg-black text-light shadow-lg p-4 rounded-4">
          <h3 class="mb-3 text-center">Login</h3>

          <!-- login form -->
          <form id="loginForm">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" id="loginEmail" placeholder="you@example.com" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" class="form-control" id="loginPassword" placeholder="••••••••" required>
            </div>
            <div class="d-flex justify-content-between">
              <a href="index.php" class="btn btn-outline-secondary">← Back</a>
              <button type="submit" class="btn btn-info" id="loginBtn">Login</button>
            </div>
          </form>

          <div class="text-center mt-3">
            <small>New here? <a href="register.php">Create an account</a></small>
          </div>
          <div id="loginMsg" class="mt-3 small text-warning"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Script to bind login button to doLogin() -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('loginForm');
      const btn  = document.getElementById('loginBtn');
      const msg  = document.getElementById('loginMsg');

      if (form) {
        form.addEventListener('submit', (e) => {
          e.preventDefault();
          doLogin().catch(err => {
            console.error(err);
            msg.textContent = 'Login error (see console).';
          });
        });
      }

      if (btn) {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          doLogin().catch(err => {
            console.error(err);
            msg.textContent = 'Login error (see console).';
          });
        });
      }
      console.log('Login handlers bound');
    });
  </script>
</body>
</html>
