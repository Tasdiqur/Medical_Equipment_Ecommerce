<?php
require_once __DIR__ . '/../lib/Response.php';
require_once __DIR__ . '/../config/config.php';

class PaymentController {
    private static function getToken() {
        $url = getenv('BKASH_BASE_URL') . "/tokenized/checkout/token/grant";
        $headers = ["Content-Type: application/json"];
        $data = [
            'app_key' => getenv('BKASH_APP_KEY'),
            'app_secret' => getenv('BKASH_APP_SECRET')
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, getenv('BKASH_USERNAME') . ":" . getenv('BKASH_PASSWORD'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $res = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($res, true);
        if (!isset($json['id_token'])) {
            throw new Exception("Failed to fetch bKash token");
        }
        return $json['id_token'];
    }

    public static function bkashInit($user) {
        $amount = 100; // TODO: get real cart total
        $token = self::getToken();

        $url = getenv('BKASH_BASE_URL') . "/tokenized/checkout/create";
        $headers = [
            "Content-Type: application/json",
            "authorization: $token",
            "x-app-key: " . getenv('BKASH_APP_KEY')
        ];

        $data = [
            'amount' => $amount,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => 'INV-' . time(),
            'callbackURL' => getenv('BKASH_CALLBACK_URL')
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $res = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($res, true);
        if (!isset($json['bkashURL'])) {
            Response::json(['error' => 'Failed to init payment', 'details' => $json], 500);
        }

        Response::json([
            'redirectUrl' => $json['bkashURL'],
            'paymentID' => $json['paymentID'],
            'amount' => $amount
        ]);
    }

    public static function bkashExecute($paymentID) {
        $token = self::getToken();

        $url = getenv('BKASH_BASE_URL') . "/tokenized/checkout/execute";
        $headers = [
            "Content-Type: application/json",
            "authorization: $token",
            "x-app-key: " . getenv('BKASH_APP_KEY')
        ];

        $data = [ 'paymentID' => $paymentID ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $res = curl_exec($ch);
        curl_close($ch);

        return json_decode($res, true);
    }
}
