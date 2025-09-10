<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Json_web_token
{

    private $secret_key;

    public function __construct()
    {
        // Baris ini mengambil kunci dari file config
        $this->secret_key = get_instance()->config->item('jwt_secret_key');
    }

    public function encode($data)
    {
        $issued_at = time();
        $expiration_time = $issued_at + (60 * 60 * 24); // Token berlaku selama 1 hari

        $payload = array(
            'iat' => $issued_at,
            'exp' => $expiration_time,
            'data' => $data
        );

        return JWT::encode($payload, $this->secret_key, 'HS256');
    }

    public function decode($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret_key, 'HS256'));
            return $decoded->data;
        } catch (Exception $e) {
            return null;
        }
    }
}
