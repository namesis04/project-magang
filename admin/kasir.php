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
    table { max-width: 100% }
    @media print {
        .no-print { display: none; }
    }
    </style>
    <script type="text/javascript">
<?php if ($p && $p['paid_at']): ?>
        $(function() { window.print() });
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
    <label for="numURUT">No. Pesanan: <input type="number" id="numURUT" name="urut"></label>
    <input type="submit" value="cek" />
</form>
<?php
if ($p) {
    $p['details'] = $db->query("SELECT menus.id,nama,harga,label FROM details INNER JOIN prices ON prices.id=details.price_id INNER JOIN menus ON menus.id = prices.menu_id WHERE urut=$p[urut]")->fetch_all(MYSQLI_ASSOC);
    $p['total'] = array_reduce($p['details'], function($c, $d) {
        return $c + $d['harga'];
    });
    if ($p['paid_at']) {
?>
<table>
    <tr><td colspan="3" align="center">
        <div>Esca Coffe</div>
        <div>Medan</div>
    </td></tr>
    <tr><td colspan="3">Pelanggan #<?php echo $p['urut'] ?></td></tr>
<?php foreach ($p['details'] as $d): ?>
    <tr>
        <td><?php echo htmlentities($d['nama'] . ($d['label'] ? " ($d[label])" : '')) ?></td>
        <td>:</td>
        <td>Rp<?php echo $d['harga'] ?></td>
    </tr>
<?php endforeach ?>
    <tr>
        <td>Total</td>
        <td>:</td>
        <td>Rp<?php echo $p['total'] ?></td>
    </tr>
    <tr><td colspan="3">Dibayar pada <?php echo $p['paid_at'] ?></td></tr>
</table>
<a href="javascript:kasir('<?php echo $p['urut'] ?>')" class="no-print">batalkan</a>
<?php
    } else {
?>
<p>
    <pre><?php echo htmlentities(json_encode($p, JSON_PRETTY_PRINT)) ?></pre>
    <a href="javascript:kasir('<?php echo $p['urut'] ?>')">buat struk</a>
</p>
<?php
    }
}
?>

</body>
</html>
