<?php
require 'config/koneksi.php';
$q = mysqli_query($conn, 'SELECT * FROM kendaraan');
print_r(mysqli_fetch_all($q, MYSQLI_ASSOC));
