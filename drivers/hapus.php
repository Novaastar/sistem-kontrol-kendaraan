<?php
require_once '../config/koneksi.php'; require_once '../config/auth.php'; require_admin();
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $q = mysqli_query($conn, "SELECT user_id FROM drivers WHERE id=$id");
    if ($q && $r = mysqli_fetch_assoc($q)) {
        $uid = $r['user_id'];
        mysqli_query($conn, "DELETE FROM drivers WHERE id=$id");
        if ($uid) {
            mysqli_query($conn, "DELETE FROM users WHERE id=$uid AND role='driver'");
        }
    }
}
header('Location: index.php');
exit;
