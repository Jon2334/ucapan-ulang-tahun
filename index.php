<?php
// Di Vercel, session tidak persisten, jadi kita hapus logic session
// Konfigurasi Nama (Ganti di sini)
$nama_penerima = "Sartika Mariani Simanjuntak"; 
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
        
        <!-- Tombol langsung ke countdown.php -->
        <a href="countdown.php" class="btn-start">Mulai Perjalanan ❤️</a>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        document.querySelector('.btn-start').addEventListener('click', function() {
            localStorage.setItem('musicPlaying', 'true');
            localStorage.setItem('audioTime', 0);
        });
    </script>
</body>
</html>