<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Memvalidasi token JWT dari header Authorization.
 * Menggunakan library Json_web_token yang sudah kita buat.
 */
function validate_jwt_token()
{
    $ci = &get_instance();
    $auth_header = $ci->input->get_request_header('Authorization');

    if (!$auth_header) {
        return null;
    }

    // Token dikirim dengan format "Bearer [token]"
    // Kita perlu memisahkan kata "Bearer" dari token-nya
    $token_parts = explode(' ', $auth_header);
    if (count($token_parts) !== 2 || $token_parts[0] !== 'Bearer') {
        return null;
    }

    $token = $token_parts[1];

    // Load library JWT kita
    $ci->load->library('json_web_token');

    // Coba decode token
    // Library akan otomatis menggunakan secret key dari config
    $decoded_data = $ci->json_web_token->decode($token);

    return $decoded_data; // Akan mengembalikan data jika valid, atau null jika tidak valid
}
