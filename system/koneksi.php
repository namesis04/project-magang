<?php
require_once __DIR__ . '/DBUtils.php';
$db = MYSQL::connect('localhost', 'root', '');
$db->select_db('esca');
