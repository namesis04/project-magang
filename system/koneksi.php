<?php
require_once __DIR__ . '/DBUtils.php';
$koneksi = MYSQL::connect('localhost', 'root', '');
$koneksi->select_db('esca');
