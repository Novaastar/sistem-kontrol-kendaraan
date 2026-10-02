<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function require_admin(): void {
    if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: /sistem_kontrol_kendaraan_bank_jatim/auth/login.php');
        exit;
    }
}

function require_driver(): void {
    if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'driver') {
        header('Location: /sistem_kontrol_kendaraan_bank_jatim/auth/login.php');
        exit;
    }
}
