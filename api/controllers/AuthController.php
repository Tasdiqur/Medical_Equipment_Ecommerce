<?php
require_once __DIR__ . '/../lib/Response.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../lib/Security.php';

class AuthController {
    public static function register() {
        $data = json_decode(file_get_contents('php://input'), true);
        $required = ['full_name','email','password','role'];
        foreach ($required as $r) if (empty($data[$r])) Response::json(['error' => "Missing $r"], 422);
        if (!in_array($data['role'], ['customer','shopowner'])) Response::json(['error'=>'Invalid role'], 422);
        if (User::findByEmail($data['email'])) Response::json(['error'=>'Email already taken'], 409);
        $user = User::create($data['full_name'], $data['email'], $data['password'], $data['role']);
        $token = User::createToken($user['id']);
        Response::json(['token'=>$token, 'user'=>['id'=>$user['id'],'full_name'=>$user['full_name'],'email'=>$user['email'],'role'=>$user['role']]], 201);
    }

    public static function login() {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $u = User::findByEmail($email);
        if (!$u || !Security::verifyPassword($password, $u['password_hash'])) {
            Response::json(['error'=>'Invalid credentials'], 401);
        }
        $token = User::createToken($u['id']);
        Response::json(['token'=>$token, 'user'=>['id'=>$u['id'],'full_name'=>$u['full_name'],'email'=>$u['email'],'role'=>$u['role']]]);
    }

    public static function me($user) {
        Response::json(['user'=>$user]);
    }

    public static function logout() {
        $headers = getallheaders();
        $token = $headers['X-Auth-Token'] ?? $headers['x-auth-token'] ?? null;
        if ($token) User::deleteToken($token);
        Response::json(['ok'=>true]);
    }
}
