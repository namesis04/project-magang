<?php
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $urut = file_get_contents('php://input');
    $p = $db->query_select('pesanan', ['urut', 'paid_at'], SQL_WHERE_CLAUSE::create([
        ['urut', '=', $urut]
    ]))->fetch_all(MYSQLI_ASSOC);
    if (empty($p)) {
        http_response_code(404);
        die('pesanan tidak terdaftar');
    }
    $p = $p[0];
    if ($p['paid_at'])
        $db->query_update('pesanan', ['paid_at' => null],
        SQL_WHERE_CLAUSE::create([
            ['urut', '=', $urut]
        ]));
    else
        $db->query_update('pesanan', ['paid_at' => date('Y-m-d H:i:s')],
        SQL_WHERE_CLAUSE::create([
            ['urut', '=', $urut]
        ]));
    exit;
}
$p = !empty($_GET['urut']) ? $_GET['urut'] : null;
if ($p) {
    $p = $db->query_select('pesanan', ['urut', 'paid_at'], SQL_WHERE_CLAUSE::create([
        ['urut', '=', $p]
    ]))->fetch_all(MYSQLI_ASSOC);
    if (is_array($p) && count($p)) $p = $p[0];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan</title>
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

        .order-list {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .order-item {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }

        .order-details {
            margin-top: 10px;
            padding-top: 10px;
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

        .pay-button {
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

        .pay-button:hover {
            background-color: #FF4733;
        }

        .back-button-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #555;
            color: #FFF;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #333;
        }

        .back-button i {
            margin-right: 5px;
        }
    </style>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript">
        async function bayar(urut) {
            await $.ajax({
                method: 'post',
                url: '',
                headers: {
                    'Content-Type': 'text/json',
                },
                data: "" + urut,
            });
            location.reload();
        }
    </script>
</head>
<body>
<div class="container">
        <h1>Daftar Pesanan</h1>
        <div class="order-list">
            <?php
            while (is_array($p = $q->fetch_assoc())):
                $p['details'] = $koneksi->query("SELECT menus.id,nama,harga,label FROM details INNER JOIN prices ON prices.id=details.price_id INNER JOIN menus ON menus.id = prices.menu_id WHERE urut=$p[urut]")->fetch_all(MYSQLI_ASSOC);
                $p['total'] = array_reduce($p['details'], function($c, $d) {
                    return $c + $d['harga'];
                });
            ?>
            <div class="order-item">
                <h3>Pelanggan #<?php echo $p['urut'] ?></h3>
                <p>Waktu Pemesanan: <?php echo $p['booked_at'] ?></p>
                <p>Total Pesanan: <?php echo count($p['details']) ?></p>
                <p>Total Harga: Rp <?php echo number_format($p['total'], 0, ',', '.') ?></p>
                <div class="order-details">
                    <?php foreach ($p['details'] as $detail): ?>
                    <div class="menu-item">
                        <h4><?php echo $detail['nama'] ?></h4>
                        <p>Harga: Rp <?php echo number_format($detail['harga'], 0, ',', '.') ?></p>
                        <p>Label: <?php echo $detail['label'] ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="pay-button" onclick="bayar('<?php echo $p['urut'] ?>')">Pelanggan Sudah Bayar</button>
            </div>
            <?php endwhile ?>
        </div>
        <div class="back-button-container">
            <a href="index.html" class="back-button"><i class="fas fa-arrow-left"></i>Kembali ke Halaman Utama</a>
        </div>
    </div>
</body>
</html>

