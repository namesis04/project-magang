<?php
require_once '../system/koneksi.php';
session_start();
$user_id = @is_numeric($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
if ($user_id && !$db->query("SELECT COUNT(*) FROM users WHERE id = $user_id")->fetch_all()[0][0]) {
    unset($_SESSION['user_id']);
    $user_id = 0;
}
if (!$user_id) {
    header('Location: ../admin.php');
    exit;
}
function auditLog($act)
{
    global $db;
    global $user_id;
    $db->query_insert('audits', ['user_id' => $user_id, 'action' => $act,]);
}
