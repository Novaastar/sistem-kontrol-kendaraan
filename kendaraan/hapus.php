<?php
require_once '../config/koneksi.php'; require_once '../config/auth.php'; require_admin();
$id=(int)($_GET['id']??0);$stmt=mysqli_prepare($conn,"DELETE FROM kendaraan WHERE id=?");mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);header('Location:index.php');exit;
