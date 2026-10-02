<?php
session_start();
// Tampilkan splash screen hanya sekali per sesi browser
if (empty($_SESSION['splash_shown'])) {
    $_SESSION['splash_shown'] = true;
    header('Location: splash.php');
    exit;
}
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard/');
} else {
    header('Location: auth/login.php');
}
exit;
