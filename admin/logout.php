<?php
require_once 'koneksi.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_destroy();
    auditLog('telah keluar');
    header('Location: ../admin.php', true, 303);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Keluar</title>
    <script type="text/javascript" src="../js/logout.js"></script>
</head>
<body>
<a href="javascript:logout()">Tekan ini jika anda yakin ingin keluar</a>

</body>
</html>
