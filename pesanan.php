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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-top: 0;
        }

        .order-number {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .order-details {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .menu-item {
            margin-bottom: 10px;
        }

        .menu-item h3 {
            margin: 0;
            color: #555;
        }

        .menu-item p {
            margin: 0;
            color: #888;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #FF6347;
            color: #FFF;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #FF4733;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Pesanan Anda</h1>
    <p class="order-number">
        <span>No. Pesanan:</span>
        <span><?php echo htmlentities(implode(', ', $_SESSION['pesanan'])) ?></span>
    </p>
    <div class="order-details">
        <?php foreach ($pesanan as $p): ?>
            <div class="menu-item">
                <h3>Pesanan <?php echo htmlentities($p['urut']) ?></h3>
                <p>Tanggal Pemesanan: <?php echo htmlentities($p['booked_at']) ?></p>
                <p>Menu:</p>
                <ul>
                    <?php foreach ($p['details'] as $detail): ?>
                        <li><?php echo htmlentities($detail['nama']) ?> - <?php echo htmlentities($detail['harga']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
    <a class="back-button" href="index.php">Kembali</a>
</body>
</html>

