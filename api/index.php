<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/lib/Response.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/ProductController.php';
require_once __DIR__ . '/controllers/OrderController.php';

cors_headers();

$method = $_SERVER['REQUEST_METHOD'];

/**
 * Path resolution that works both with and without Apache rewrites.
 * - Prefer query-string param r (e.g., api/index.php?r=/products?limit=12)
 * - Fallback to parsing REQUEST_URI when rewrite is enabled
 */
$req = isset($_GET['r']) ? $_GET['r'] : '';
if ($req === '' || $req === null) {
    $uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    // App base folder, e.g., /medrex-php
    $base = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');

    // When URL looks like /medrex-php/api/index.php/...
    if (strpos($uri, $base . '/api/index.php') === 0) {
        $req = substr($uri, strlen($base . '/api/index.php'));
    } else {
        // Or /medrex-php/api/...
        $req = substr($uri, strlen($base . '/api'));
    }
}

/* If r contains its own query (e.g. /products?limit=12), take just the path */
$reqPath = parse_url($req, PHP_URL_PATH);
if ($reqPath) $req = $reqPath;

$path = '/' . ltrim($req, '/');

/* KEY NORMALIZATION: always prefix with /api so routes match below */
if (strpos($path, '/api/') !== 0) {
    $path = ($path === '/') ? '/api' : '/api' . $path;
}

// Routes
try {
    // Auth
    if ($path === '/api/auth/register' && $method === 'POST') { return AuthController::register(); }
    if ($path === '/api/auth/login'    && $method === 'POST') { return AuthController::login();    }
    if ($path === '/api/auth/me'       && $method === 'GET')  { $u = AuthMiddleware::requireAuth();           return AuthController::me($u); }
    if ($path === '/api/auth/logout'   && $method === 'POST') { return AuthController::logout();   }

    // Products (public + seller)
    if ($path === '/api/products'      && $method === 'GET')  { return ProductController::list();  }
    if ($path === '/api/products/mine' && $method === 'GET')  { $u = AuthMiddleware::requireAuth('shopowner'); return ProductController::byOwner($u); }
    if (preg_match('#^/api/products/(\d+)$#', $path, $m)) {
        $id = (int)$m[1];
        if ($method === 'GET')    { return ProductController::get($id); }
        if ($method === 'PUT')    { $u = AuthMiddleware::requireAuth('shopowner'); return ProductController::update($u, $id); }
        if ($method === 'DELETE') { $u = AuthMiddleware::requireAuth('shopowner'); return ProductController::delete($u, $id); }
    }
    if ($path === '/api/products' && $method === 'POST') {
        $u = AuthMiddleware::requireAuth('shopowner');
        return ProductController::create($u);
    }
    //payments
    if ($path === '/api/payment/bkash-init' && $method === 'POST') {
    $u = AuthMiddleware::requireAuth('customer');
    require_once __DIR__ . '/controllers/PaymentController.php';
    return PaymentController::bkashInit($u);
}

if ($path === '/api/payment/bkash-execute' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    require_once __DIR__ . '/controllers/PaymentController.php';
    $res = PaymentController::bkashExecute($input['paymentID']);
    Response::json($res);
}



    // Orders (customer)
    if ($path === '/api/orders'   && $method === 'POST') { $u = AuthMiddleware::requireAuth('customer'); return OrderController::create($u); }
    if ($path === '/api/orders'   && $method === 'GET')  { $u = AuthMiddleware::requireAuth('customer'); return OrderController::list($u);  }

    Response::json(['error' => 'Route not found', 'path' => $path, 'method' => $method], 404);
} catch (Throwable $e) {
    Response::json(['error' => 'Server error', 'details' => $e->getMessage()], 500);
}
