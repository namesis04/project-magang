<?php
require_once 'system/koneksi.php';
session_start();
if (is_array($_SESSION['pesanan']))
    $_SESSION['pesanan'] = array_unique($_SESSION['pesanan']);
else $_SESSION['pesanan'] = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db->query_insert('pesanan', ['booked_at' => date('Y-m-d H:i:s')]);
    $p = $db->insert_id();
    $db->query(sprintf('INSERT INTO details (price_id, urut) VALUES %s',
    implode(', ', array_map(function($val) use ($db, $p) {
        return sprintf('(%s)', implode(', ', array_map(function($val) use ($db) {
            return SQL::escape_valstr($val, true, $db);
        }, [$val, $p])));
    }, json_decode(file_get_contents('php://input'))))));
    $_SESSION['pesanan'][] = $p;
    http_response_code(204);
    exit;
}
$pesanan = array_map(function($p) use ($db) {
    $w = SQL_WHERE_CLAUSE::create([
        ['urut', '=', $p, ],
    ]);
    $p = $db->query("SELECT * FROM pesanan $w AND (delivered_at IS NULL OR paid_at IS NULL)")->fetch_all(MYSQLI_ASSOC);
    if (count($p)) {
        $p = $p[0];
        $p['details'] = $db->query("SELECT menus.id,nama,harga,label FROM details INNER JOIN prices ON prices.id=details.price_id INNER JOIN menus ON menus.id = prices.menu_id $w")->fetch_all(MYSQLI_ASSOC);
        return $p;
    }
}, $_SESSION['pesanan']);
$pesanan = array_values(array_filter($pesanan, function($p) {
    return !is_null($p);
}));
$_SESSION['pesanan'] = array_map(function($p) {
    return $p['urut'];
}, $pesanan);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Anda</title>
</head>
<body>
<p>
    <span>No. Pesanan:</span>
    <span><?php echo htmlentities(implode(', ', $_SESSION['pesanan'])) ?></span>
</p>
<pre><?php echo json_encode($pesanan, JSON_PRETTY_PRINT) ?></pre>

</body>
</html>
