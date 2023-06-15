<?php
require_once 'system/koneksi.php';
session_start();
$user_id = @is_numeric($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
if ($user_id && !$db->query("SELECT COUNT(*) FROM users WHERE id = $user_id")->fetch_all()[0][0]) {
    unset($_SESSION['user_id']);
    $user_id = 0;
}
if (@$_POST['username'] && @$_POST['password']) {
    if (@$_POST['fn']) {
        $frm = $_POST;
        $frm['password'] = ['PASSWORD', $frm['password'],];
        $db->query_insert('users', $frm);
        if ($user_id)
            $db->query_insert('audits', ['user_id' => $user_id, 'action' => 'mendaftar admin baru',]);
        else $_SESSION['user_id'] = $db->insert_id();
    } elseif (!$user_id) {
        $_SESSION['user_id'] = $db->query_select('users', ['id'], SQL_WHERE_CLAUSE::create([
            ['username', '=', $_POST['username']],
            SQL_WHERE_CLAUSE::LOGICAL_AND,
            ['password', '=', ['PASSWORD', $_POST['password'],]],
        ]))->fetch_all()[0][0];
        if (!$_SESSION['user_id']) {
            unset($_SESSION['user_id']);
            die('<script>alert("coba lagi"); history.back()</script>');
        }
    }
    if (!$user_id) {
        $db->query_insert('audits', ['user_id' => $_SESSION['user_id'], 'action' => 'telah masuk',]);
    }
    header('Location: admin');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Login</title>
  <!-- Include Tailwind CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style1.css">"
  
</head>

<body>
  <div class="flex justify-center items-center min-h-screen">
    <div class="w-full max-w-md">
      <h1 class="text-4xl golden-text text-center font-bold mb-4">Selamat Datang di Esca Caffe</h1>
      <p class="text-lg golden-text1 text-center font-bold mb-8">Silahkan Login</p>
<?php if (!$user_id): ?>
      <form method="post" class="form-background shadow-md rounded px-8 pt-6 pb-8 mb-4" id="loginForm">
        <!-- Login Form -->
        <div class="mb-4">
          <label class="block text-gray-900 text-sm font-bold mb-2" for="username">Username</label>
          <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-900 leading-tight focus:outline-none focus:shadow-outline" name="username" id="username" type="text" placeholder="Username" required>
        </div>
        <div class="mb-6">
          <label class="block text-gray-900 text-sm font-bold mb-2" for="password">Password</label>
          <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-900 leading-tight focus:outline-none focus:shadow-outline" name="password" id="password" type="password" placeholder="********" required>
        </div>
        <div class="flex items-center justify-between">
          <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" value="Sign In" required>
          <a class="inline-block align-baseline font-bold text-sm forgot-password" href="#">
            Forgot Password?
          </a>
        </div>
      </form>
<?php endif ?>

      <!-- Registration Form - Initially Hidden -->
      <div<?php if (!$user_id) echo ' class="hidden"' ?> id="registrationForm">
        <form method="post" class="form-background shadow-md rounded px-8 pt-6 pb-8 mb-4">
          <h2 class="text-2xl golden-text font-bold mb-4">Admin Registration</h2>
          <div class="mb-4">
            <label class="block text-gray-900 text-sm font-bold mb-2" for="fullname">Full Name</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-900 leading-tight focus:outline-none focus:shadow-outline" name="fn" id="fullname" type="text" placeholder="Full Name" required>
          </div>
          <div class="mb-4">
            <label class="block text-gray-900 text-sm font-bold mb-2" for="username_reg">Username</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-900 leading-tight focus:outline-none focus:shadow-outline" name="username" id="username_reg" type="text" placeholder="Username" required>
          </div>
          <div class="mb-4">
            <label class="block text-gray-900 text-sm font-bold mb-2" for="email">Username</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-900 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" placeholder="Email">
          </div>
          <div class="mb-4">
            <label class="block text-gray-900 text-sm font-bold mb-2" for="password_reg">Password</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-900 leading-tight focus:outline-none focus:shadow-outline" name="password" id="password_reg" type="password" placeholder="********" required>
          </div>
          <div class="mb-6">
            <label class="block text-gray-900 text-sm font-bold mb-2" for="confirm_password">Confirm Password</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-900 leading-tight focus:outline-none focus:shadow-outline" id="confirm_password" type="password" placeholder="********" required>
          </div>
          <div class="flex items-center justify-between">
            <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" value="Register" required>
          </div>
        </form>
      </div>
<?php if (!$user_id): ?>
      <!-- Registration Button -->
      <div class="flex items-center justify-center">
        <button id="registrationButton" class="bg-green-700 hover:bg-green-900 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4">
          Registration
        </button>
      </div>
<?php endif ?>
    </div>
  </div>

  <script>
    // Toggle visibility of the registration form
    const registrationButton = document.getElementById('registrationButton');
    const registrationForm = document.getElementById('registrationForm');
    const loginForm = document.getElementById('loginForm');

    document.getElementById('registrationForm').addEventListener('submit', function(e) {
      if (document.getElementById('confirm_password').value != document.getElementById('password_reg').value) {
        e.preventDefault();
        alert('password dan konfirmasi harus sama');
      }
    });

    registrationButton.addEventListener('click', () => {
      registrationForm.classList.toggle('hidden');
      loginForm.classList.toggle('hidden');
      if (registrationForm.classList.contains('hidden')) {
        registrationButton.textContent = 'Registration';
      } else {
        registrationButton.textContent = 'Back to Login';
      }
    });
  </script>
</body>

</html>
