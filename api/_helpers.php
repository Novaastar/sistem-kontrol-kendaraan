<?php
// Pastikan PHP error tidak tampil sebagai HTML (mencegah FormatException di Flutter)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);

require_once '../config/koneksi.php';
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
function json_out($data,$status=200){http_response_code($status);echo json_encode($data,JSON_UNESCAPED_UNICODE);exit;}
function body(){return json_decode(file_get_contents('php://input'),true) ?: [];}
function auth_user(){
  $h = '';
  if (isset($_SERVER['HTTP_AUTHORIZATION'])) { $h = trim($_SERVER['HTTP_AUTHORIZATION']); }
  elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) { $h = trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']); }
  elseif (function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) { $h = trim($headers['Authorization']); }
  }
  if(!preg_match('/Bearer\s+(.+)/i',$h,$m)) json_out(['success'=>false,'message'=>'Token diperlukan'],401);
  $hash=hash('sha256',trim($m[1]));
  $stmt=mysqli_prepare($GLOBALS['conn'],"SELECT u.id,u.username,u.role,u.nama FROM api_tokens t JOIN users u ON u.id=t.user_id WHERE t.token_hash=? AND (t.expires_at IS NULL OR t.expires_at>NOW()) LIMIT 1");
  mysqli_stmt_bind_param($stmt,'s',$hash);mysqli_stmt_execute($stmt);$u=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
  if(!$u) json_out(['success'=>false,'message'=>'Token tidak valid atau kedaluwarsa'],401);
  return $u;
}
