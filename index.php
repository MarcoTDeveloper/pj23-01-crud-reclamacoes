<?php
date_default_timezone_set('America/Sao_Paulo');
define('VERSION', '23.7.0');

$url = isset($_GET['url']) ? $_GET['url'] : '';
$url = explode('/', $url);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
header('API-Version: ' . VERSION);

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$headers = apache_request_headers();
$json = file_get_contents('php://input');
$request = json_decode($json);

require_once 'src/services/api_configuration.php';
require_once 'src/routers.php';