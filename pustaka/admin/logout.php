<?php
session_start();

// Hapus semua variabel session
$_SESSION = [];

// Hancurkan session
session_destroy();

// Redirect ke halaman login dengan pesan atau langsung
header("Location: login.php");
exit;
?>