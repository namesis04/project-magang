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
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <style type="text/css">
        table {
            max-width: 100%;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        /* Custom styles for displaying the order list */
        .order-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .order-details {
            margin-bottom: 20px;
            font-family : verdana;
            font-size : 0.9rem;
        }
        .order-details h3 {
            margin-top: 0;
            text-align : center;
            font-family : verdana;
            font-size : 1.5rem;
        }

        .order-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-details table th,
        .order-details table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .order-details table th {
            background-color: #f2f2f2;
        }

        .order-details table td:last-child {
            text-align: right;
        }

        .order-actions {
            text-align: center;
        }

        .order-actions a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #994d00;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .search-container {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-container input[type="number"] {
            padding: 5px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .search-container input[type="submit"] {
            padding: 5px 20px;
            font-size: 1rem;
            background-color: #b35900;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-kembali {
            display: inline-block;
            background-color: #ff9933;
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }

        .btn-kembali:hover {
            background-color: #ff9933;
        }
    </style>
    <script type="text/javascript">
        <?php if ($p && $p['paid_at']): ?>
        $(function () {
            window.print()
        });
        <?php endif ?>
        async function kasir(urut) {
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
<form class="no-print">
    <div class="search-container">
    <label for="numURUT">No. Pesanan: <input type="number" id="numURUT" name="urut"></label>
    <input type="submit" value="Cek"/>
    </div>
</form>
<?php
if ($p) {
    $p['details'] = $db->query("SELECT menus.id,nama,harga,label FROM details INNER JOIN prices ON prices.id=details.price_id INNER JOIN menus ON menus.id = prices.menu_id WHERE urut=$p[urut]")->fetch_all(MYSQLI_ASSOC);
    $p['total'] = array_reduce($p['details'], function ($c, $d) {
        return $c + $d['harga'];
    });
    if ($p['paid_at']) {
        ?>
        <div class="order-container">
            <div class="order-details">
                <h3>Esca Coffe</h3>
                <div>Medan</div>
                <h4>Pelanggan #<?php echo $p['urut'] ?></h4>
                <table>
                    <?php foreach ($p['details'] as $d): ?>
                        <tr>
                            <td><?php echo htmlentities($d['nama'] . ($d['label'] ? " ($d[label])" : '')) ?></td>
                            <td>Rp<?php echo $d['harga'] ?></td>
                        </tr>
                    <?php endforeach ?>
                    <tr>
                        <th>Total</th>
                        <td>Rp<?php echo $p['total'] ?></td>
                    </tr>
                    <tr>
                        <th>Dibayar pada</th>
                        <td><?php echo $p['paid_at'] ?></td>
                    </tr>
                </table>
            </div>
            <div class="order-actions">
                <a href="javascript:kasir('<?php echo $p['urut'] ?>')" class="no-print">Batalkan</a>
                <a href="javascript:window.print()" class="no-print">Cetak</a>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="order-container">
            <div class="order-details">
                <h3>Pesanan</h3>
                <pre><?php echo htmlentities(json_encode($p, JSON_PRETTY_PRINT)) ?></pre>
            </div>
            <div class="order-actions">
                <a href="javascript:kasir('<?php echo $p['urut'] ?>')">Buat Struk</a>
                <a href="index.php" class="btn-kembali">Kembali</a>
            </div>
            <a href="index.php" class="btn-kembali">Kembali</a>
        </div>
        <?php
    }
}
?>


</body>
</html>
