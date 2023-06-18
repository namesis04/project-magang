<?php
require_once 'koneksi.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $urut = file_get_contents('php://input');
    $db->query_update('pesanan', ['delivered_at' => date('Y-m-d H:i:s')],
    SQL_WHERE_CLAUSE::create([
        ['urut', '=', $urut]
    ]));
    http_response_code(204);
    auditLog("mengantarkan pesanan #$urut");
    exit;
}
$q = $db->query_select('pesanan', '*', SQL_WHERE_CLAUSE::create([
    ['delivered_at', SQL_WHERE_CLAUSE::OPERATOR_ISNULL]
]));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan</title>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .pesanan {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }

        .pesanan ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        .pesanan li {
            margin-bottom: 5px;
        }

        .pesanan a {
            display: inline-block;
            background-color: #804000;
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }

        .pesanan a:hover {
            background-color: #ff8c1a;
        }

        .pesanan p {
            margin-top: 10px;
        }
        .btn-kembali {
            display: inline-block;
            background-color: #ff8c1a;
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }

        .btn-kembali:hover {
            background-color: #73481f;
        }
    </style>
    <script type="text/javascript">
        async function antar(urut) {
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
<?php
while (is_array($p = $q->fetch_assoc())):
    $p['details'] = $db->query("SELECT menus.id,nama,harga,label FROM details INNER JOIN prices ON prices.id=details.price_id INNER JOIN menus ON menus.id = prices.menu_id WHERE urut=$p[urut]")->fetch_all(MYSQLI_ASSOC);
    $p['total'] = array_reduce($p['details'], function($c, $d) {
        return $c + $d['harga'];
    });
?>
<div class="pesanan">
    <ul>
        <?php foreach ($p['details'] as $detail): ?>
            <li><?php echo $detail['nama']; ?> - Rp <?php echo number_format($detail['harga']); ?></li>
        <?php endforeach; ?>
    </ul>
    <p>Total: Rp <?php echo number_format($p['total']); ?></p>
    <a href="javascript:antar('<?php echo $p['urut'] ?>')">antar ke pelanggan <?php echo htmlentities('#' . $p['urut']) ?></a>
</div>
<?php endwhile ?>
<a href="index.php" class="btn-kembali">Kembali</a>

</body>
</html>
