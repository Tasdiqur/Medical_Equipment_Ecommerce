<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../lib/Response.php';

class AuthMiddleware {
    public static function requireAuth($role = null) {
        $headers = getallheaders();
        $token = $headers['X-Auth-Token'] ?? $headers['x-auth-token'] ?? null;
        if (!$token) Response::json(['error' => 'Missing X-Auth-Token'], 401);
        $user = User::getByToken($token);
        if (!$user) Response::json(['error' => 'Invalid or expired token'], 401);
        if ($role && $user['role'] !== $role) {
            Response::json(['error' => 'Forbidden: role '.$user['role'].' cannot access'], 403);
        }
        return $user;
    }
}
