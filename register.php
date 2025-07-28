<!doctype html>
<html lang="en">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MedRex</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/styles.css" rel="stylesheet">
<script defer src="assets/js/app.js"></script>

</head>
<body class="bg-dark text-light d-flex align-items-center" style="min-height:100vh">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-7 col-lg-6">
        <div class="card bg-black text-light shadow-lg p-4 rounded-4">
          <h3 class="mb-3 text-center">Create account</h3>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Full name</label>
              <input type="text" class="form-control" id="regName">
            </div>
            <div class="col-12">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" id="regEmail">
            </div>
            <div class="col-12">
              <label class="form-label">Password</label>
              <input type="password" class="form-control" id="regPassword">
            </div>
            <div class="col-12">
              <label class="form-label">Role</label>
              <select class="form-select" id="regRole">
                <option value="customer" selected>Customer</option>
                <option value="shopowner">Shop Owner</option>
              </select>
            </div>
            <div class="col-12 d-flex justify-content-between">
              <a href="index.php" class="btn btn-outline-secondary">← Back</a>
              <button class="btn btn-info" onclick="doRegister()">Register</button>
            </div>
          </div>
          <div id="regMsg" class="mt-3 small text-warning"></div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
