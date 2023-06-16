<?php
require_once 'koneksi.php';
$auditLog = $db->query('SELECT * FROM audits INNER JOIN users ON users.id = audits.user_id LIMIT 15');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Administrator</title>
  <!-- Include Tailwind CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="style2.css">
</head>

<body>
    <div class="flex h-screen">
    <!-- Sidebar -->
    <div class="w-1/4 sidebar flex flex-col items-center">
      <div class="py-4 px-8">
        <div class="flex items-center mb-4">
        <img src="logo.png" alt="" class="h-12 w-12 mr-4 mb-4">
        <h1 class="text-2xl font-bold mb-4">Admin Esca Coffe</h1>
        </div>
        <ul class="space-y-2">
          <li>
            <a href="pesanan.php" class="block py-2 px-4 rounded-lg button-pesanan hover:bg-yellow-600">Admin Pesanan</a>
          </li>
          <li>
            <a href="kasir.php" class="block py-2 px-4 rounded-lg button-kasir hover:bg-yellow-600">Admin Kasir</a>
          </li>
          <li>
            <a href="../admin.php" class="block py-2 px-4 rounded-lg button-kasir hover:bg-yellow-600">Tambah Admin</a>
          </li>
          <li>
            <form action="../admin.php" method="post">
              <button type="submit" class="block py-2 px-4 rounded-lg button-logout hover:bg-red-600">Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
    
    <!-- Main Content -->
    <div class="flex-1 p-8 bg-sidebar">
      <h2 class="text-2xl font-bold mb-4">Selamat Datang di Dashboard Admin Esca Coffe!</h2>
<?php if ($auditLog->num_rows()): ?>
      <table style="border-collapse: separate" cellSpacing="5">
          <thead>
              <tr>
                  <td>Waktu</td>
                  <td>Pelaku</td>
                  <td>Aksi</td>
              </tr>
          </thead>
          <tbody>
<?php while (is_array($log = $auditLog->fetch_array())): ?>
              <tr>
                  <td className="pr-4"><?php echo $log['waktu'] ?></td>
                  <td><?php echo htmlentities($log['fn']) ?></td>
                  <td><?php echo htmlentities($log['action']) ?></td>
              </tr>
<?php endwhile ?>
          </tbody>
      </table>
<?php else: ?>
      <p>Anda dapat memilih salah satu menu di samping.</p>
<?php endif ?>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer">
    <p>Copyright &copy; by Admin. All Right Reserved</p>
  </footer>
<style>
  .footer {
    background-color: #1f0e01;
      color: #FFD700;
      text-align: center;
      padding: 1rem;
 }
</style>
</body>

</html>
