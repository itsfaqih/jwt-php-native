<?php
// Import script autoload agar bisa menggunakan library
require_once('./vendor/autoload.php');
require_once('./cors.php');
// Import library
use Firebase\JWT\JWT;
use Dotenv\Dotenv;

// Load dotenv
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Atur jenis response
header('Content-Type: application/json');

// Cek method request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  http_response_code(405);
  exit();
}

// Cek cookie refreshToken
if(!isset($_COOKIE['refreshToken'])) {
  http_response_code(403);
  exit();
}

try {
  // Men-decode token. Dalam library ini juga sudah sekaligus memverfikasinya
  $refresh_payload = JWT::decode($_COOKIE['refreshToken'], $_ENV['REFRESH_TOKEN_SECRET'], ['HS256']);

  $waktu_kadaluarsa = time() + (15 * 60);

  // Payload untuk token baru
  $payload = [
    'email' => $refresh_payload->email,
    // Di library ini wajib menambah key exp untuk mengatur masa berlaku token
    'exp' => $waktu_kadaluarsa
  ];
  
  // Men-generate access token
  $access_token = JWT::encode($payload, $_ENV['ACCESS_TOKEN_SECRET']);

  // Kirim token ke user
  echo json_encode([
    'accessToken' => $access_token,
    'expiry' => date(DATE_ISO8601, $waktu_kadaluarsa)
  ]);
  
  $payload['exp'] = time() + (60 * 60);
  $refresh_token = JWT::encode($payload, $_ENV['REFRESH_TOKEN_SECRET']);
  
  // Simpan refresh token baru di http-only cookie
  setcookie('refreshToken', $refresh_token, $payload['exp'], '', '', false, true);
} catch (Exception $e) {
  // Bagian ini akan jalan jika terdapat error saat JWT diverifikasi atau di-decode
  http_response_code(401);
  exit();
}
