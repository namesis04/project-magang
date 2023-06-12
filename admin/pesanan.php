<?php
require_once '../system/koneksi.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $urut = file_get_contents('php://input');
    $koneksi->query_update('pesanan', ['delivered_at' => date('Y-m-d H:i:s')],
    SQL_WHERE_CLAUSE::create([
        ['urut', '=', $urut]
    ]));
    exit;
}
$q = $koneksi->query_select('pesanan', '*', SQL_WHERE_CLAUSE::create([
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
    $p['details'] = $koneksi->query("SELECT menus.id,nama,harga,label FROM details INNER JOIN prices ON prices.id=details.price_id INNER JOIN menus ON menus.id = prices.menu_id WHERE urut=$p[urut]")->fetch_all(MYSQLI_ASSOC);
    $p['total'] = array_reduce($p['details'], function($c, $d) {
        return $c + $d['harga'];
    });
?>
<p>
    <pre><?php echo htmlentities(json_encode($p, JSON_PRETTY_PRINT)) ?></pre>
    <a href="javascript:antar('<?php echo $p['urut'] ?>')">antar ke pelanggan <?php echo htmlentities('#' . $p['urut']) ?></a>
</p>
<?php endwhile ?>

</body>
</html>
