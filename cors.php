<?php
require_once('vendor/autoload.php');
use Dotenv\Dotenv;

// Load dotenv
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Mengatasi CORS
header('Access-Control-Allow-Origin: '.$_ENV['FRONTEND_URL']);
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: HEAD, DELETE, POST, PUT, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Expose-Headers: Authorization');