<?php
// Di Vercel session tidak bisa diandalkan antar halaman
// Jadi kita set manual lagi namanya di sini agar aman
$name = "Cintaku"; // Pastikan sama dengan index.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Waktu...</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&family=Poppins:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <p>Hai, <?= htmlspecialchars($name) ?>...</p>
        <p>Sebentar lagi momen spesial itu tiba.</p>
        <h1 id="countdown" style="font-size: 4rem; margin: 20px 0;">00:00:00</h1>
        <p>Tunggu sampai waktu habis...</p>
    </div>

    <!-- Kontrol Musik -->
    <button id="musicControl" style="position: fixed; top: 20px; right: 20px; z-index: 999; background: rgba(0,0,0,0.5); color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer;">🎵 ON</button>
    <audio id="bgm" loop>
        <source src="assets/music/bgm.mp3" type="audio/mpeg">
    </audio>

    <script src="assets/js/script.js"></script>
    <script>
        // SET TANGGAL ULANG TAHUN DI SINI (Format: YYYY-MM-DD HH:MM:SS)
        // Contoh untuk testing: 10 detik dari sekarang
        const targetDate = new Date().getTime() + 10000; 
        
        // UNTUK PRODUCTION GANTI LINE DI ATAS DENGAN TANGGAL ASLI:
        // const targetDate = new Date("2024-10-25 00:00:00").getTime();

        const timer = setInterval(function() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("countdown").innerHTML = 
                (days > 0 ? days + "d " : "") + hours + "h " + minutes + "m " + seconds + "s ";

            if (distance < 0) {
                clearInterval(timer);
                window.location.href = "surat.php";
            }
        }, 1000);
    </script>
</body>
</html>