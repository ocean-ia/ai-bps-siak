
<?php
// Set header untuk mengizinkan CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Jika request adalah OPTIONS, kembalikan header saja
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Route request ke file yang sesuai
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Hapus trailing slash jika ada
$path = rtrim($path, '/');

// Route ke endpoint yang sesuai
switch ($path) {
    case '/api/chat':
        require_once 'api/chat.php';
        break;
    default:
        // Halaman tidak ditemukan
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;
}
?>
