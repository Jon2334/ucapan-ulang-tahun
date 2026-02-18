<?php
session_start();

// --- KONFIGURASI NAMA PENERIMA (HARDCODED) ---
// Ganti "Cintaku" di bawah ini dengan nama panggilan kesayangan dia.
$nama_penerima = "Cintaku"; 

// Simpan nama ke dalam session secara otomatis agar bisa dipakai di halaman lain
$_SESSION['sender_name'] = $nama_penerima;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Untukmu, <?= htmlspecialchars($nama_penerima) ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&family=Poppins:wght@300;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Sedikit penyesuaian khusus untuk halaman index baru */
        .btn-start {
            display: inline-block;
            text-decoration: none;
            margin-top: 30px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 117, 140, 0.7); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(255, 117, 140, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 117, 140, 0); }
        }
    </style>
</head>
<body>
    
    <div class="container fade-in">
        <h1>Hai, <?= htmlspecialchars($nama_penerima) ?>...</h1>
        <p style="margin-top: 20px; font-size: 1.1rem; line-height: 1.6;">
            "Di antara miliaran manusia di dunia…<br>
            aku bersyukur semesta mempertemukan kita."
        </p>
        <br>
        <p style="font-size: 0.9rem; opacity: 0.8;">Ada sesuatu kecil yang ingin kutunjukkan padamu.</p>
        
        <!-- Tombol langsung ke countdown.php karena nama sudah di-set -->
        <a href="countdown.php" class="btn-start">Mulai Perjalanan ❤️</a>
    </div>

    <!-- Script Load -->
    <script src="assets/js/script.js"></script>
    <script>
        // Logika Memulai Musik
        document.querySelector('.btn-start').addEventListener('click', function() {
            // 1. Set status musik agar auto-play di halaman berikutnya
            localStorage.setItem('musicPlaying', 'true');
            // 2. Reset waktu lagu ke 0 agar mulai dari awal
            localStorage.setItem('audioTime', 0);
        });
    </script>
</body>
</html>