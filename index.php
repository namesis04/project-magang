<?php
require_once 'system/koneksi.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esca Coffe</title>
    
    <!-- Menggunakan Font Google -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Gunakan style.css -->
    <link rel="stylesheet" href="css/style.css">

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

    <script type="text/json" id="prices"><?php echo json_encode($koneksi->query('SELECT prices.id, nama, harga, label, menu_id FROM prices INNER JOIN menus ON menus.id = prices.menu_id')->fetch_all(MYSQLI_ASSOC)) ?></script>
</head>
<body>
    <!-- section awal header  -->
<header class="header">

    <a href="#" class="logo">
        <img src="images/logo.png" alt="">
    </a>

    <nav class="navbar">
        <a href="#home">Home</a>
        <a href="#about">About</a>
        <a href="#menu">Menu</a>
        <a href="#contact">Contact</a>
    </nav> 

    <div class="icons">
        <div class="fas fa-search" style="visibility: hidden" id="search-btn"></div>
        <div class="fas fa-shopping-cart" id="cart-btn"></div>
        <div class="fas fa-bars" id="menu-btn"></div>
    </div>

    <div class="search-form">
        <input type="search" id="search-box" placeholder="Search here...">
        <label for="search-box" class="fas fa-search"></label>
    </div>

    <div class="cart-items-container">
        <a href="#" class="btn">Checkout</a>
    </div>

</header>

    <!-- section akhir header  -->
    <!-- halaman utama awal  -->
    <section class="home" id="home">
        <div class="content">
            <h3>Selamat Datang di Esca Coffe</h3>
            <p>We are Open Every Day</p>
            <a href="#menu" class="btn">Buy Now</a>
        </div>
    </section>
    <!-- halaman utama akhir  -->
  
    <!-- bagian awal section about  -->

    <section class="about" id="about">

        <h1 class="heading"><span>About</span> Us</h1>

        <div class="row">
            <div class="image">
                <img src="images/about.png" alt="">
            </div>

            <div class="content">
                <h3>Apa itu Esca Coffe ?</h3>
                <p>Selamat datang di Esca Cafe, coffeeshop kece yang bikin kamu betah lama-lama nongkrong di sini! Kita ada di tengah kota, dengan interior yang modern dan simpel, penuh meja kayu dan sofa empuk yang enak buat nyantai dan kerja bareng!!</p>
                <p>Kita punya banyak jenis kopi mulai dari espresso sampai latte yang creamy, menggunakan biji kopi pilihan yang dipanggang setiap hari supaya menghasilkan rasa yang nikmat. Kita juga menyediakan minuman non coffe seperti teh yang bisa kamu nikmati. Kita juga menyediakan makanan
                    seperti nasi goreng, mie goreng dan aneka makanan lainnya yang dapat mengganjal perut kamu ketika lapar.
                </p>
                <!-- <a href="#" class="btn">Learn More</a> -->
            </div>

        </div>


    </section>

     <!-- bagian akhir section about  -->

     <!-- menu section awal -->

     <section class="menu" id="menu">

        <h1 class="heading">Our <span>Menu</span></h1>
        <div class="box-container" id="menus">
        </div>
     </section>


    <!-- menu section akhir -->

     <!-- section kontak -->
     <section class="contact" id="contact">
        <h1 class="heading"> <span>Contact</span> Us</h1>
        <div class="row">
            
            <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127426.96233194097!2d98.47049916371913!3d3.5661649614216357!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30312513a6eee8a7%3A0x1e4aacfec6845335!2sESCA%20KOPI!5e0!3m2!1sid!2sid!4v1685021308038!5m2!1sid!2sid" 
            allowfullscreen="" loading="lazy"></iframe>
            
            <form action="">
                <h3>Hubungi Kami</h3>
                <div class="inputBox">
                    <span class="fas fa-user"></span>
                    <input type="text" placeholder="name">
                </div>
                <div class="inputBox">
                    <span class="fas fa-envelope"></span>
                    <input type="email" placeholder="email">
                </div>
                <div class="inputBox">
                    <span class="fas fa-phone"></span>
                    <input type="number" placeholder="number">
                </div>
                <input type="submit" value="Contact Now" class="btn">
            </form>
        </div>
     </section>

    <!-- section footer -->
    <section class="footer">
        <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-pinterest"></a>
        </div>

        <div class="links">
            <a href="#">Home</a>
            <a href="#">About</a>
            <a href="#">Menu</a>
            <a href="#">Contact</a>
        </div>

        <div class="credit">Create by <span>Admin</span> | All Right Reserved</div>
    </section>

    <!-- link untuk java script -->
    <script src="js/script.js"></script>

</body>
</html>