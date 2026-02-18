<?php
session_start();
$nama_penerima = isset($_SESSION['sender_name']) ? $_SESSION['sender_name'] : "Sayang";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Happy Birthday!</title>
    <link href="[https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&family=Poppins:wght@300;600&display=swap](https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&family=Poppins:wght@300;600&display=swap)" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .confetti { position: absolute; width: 10px; height: 10px; background: #ffd1dc; animation: fall 3s linear infinite; }
        h1 { line-height: 1.2; margin-bottom: 10px; margin-top: 10px; text-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        
        /* --- EFEK MASUK (FADE IN DARI HITAM) --- */
        #entrance-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: black; /* Warna harus sama dengan exit surat.php */
            z-index: 10000; /* Paling atas */
            pointer-events: none; /* Agar bisa diklik tembus */
            animation: fadeOutOverlay 2.5s ease-in-out forwards; /* Durasi animasi masuk */
        }

        @keyframes fadeOutOverlay {
            0% { opacity: 1; } /* Mulai dari Gelap */
            100% { opacity: 0; } /* Menjadi Transparan */
        }

        /* --- KUE ULANG TAHUN REALISTIS --- */
        .cake-container {
            position: relative;
            width: 200px;
            height: 160px;
            margin: 50px auto 20px; 
            display: flex;
            justify-content: center;
            align-items: flex-end;
        }
        .plate {
            position: absolute; bottom: 0; width: 220px; height: 12px;
            background: #f5f5f5; border-radius: 50%; box-shadow: 0 5px 15px rgba(0,0,0,0.4); z-index: 1;
        }
        .cake-body {
            position: absolute; bottom: 6px; width: 180px; height: 85px;
            background: linear-gradient(to right, #6a1b9a 0%, #9c27b0 30%, #8e24aa 60%, #6a1b9a 100%);
            border-radius: 10px 10px 5px 5px; box-shadow: inset 0 -5px 10px rgba(0,0,0,0.3); z-index: 2;
        }
        .cake-top {
            position: absolute; bottom: 85px; width: 184px; left: 50%; transform: translateX(-50%);
            height: 40px; background: #f48fb1; border-radius: 50% 50% 10px 10px; z-index: 3;
            box-shadow: 0 3px 5px rgba(0,0,0,0.1);
        }
        .drips {
            position: absolute; top: 25px; width: 100%; display: flex; justify-content: space-between;
            padding: 0 8px; z-index: 3;
        }
        .drip {
            width: 15px; height: 25px; background: #f48fb1; border-radius: 0 0 15px 15px;
            box-shadow: 1px 2px 3px rgba(0,0,0,0.1);
        }
        .drip:nth-child(2) { height: 15px; } .drip:nth-child(4) { height: 18px; }

        .candles {
            position: absolute; bottom: 105px; width: 100%; display: flex;
            justify-content: center; gap: 8px; z-index: 4;
        }
        .number-candle {
            font-family: 'Poppins', sans-serif; font-weight: 900; font-size: 60px;
            color: #FFD700; background: linear-gradient(to bottom, #fff59d, #fbc02d);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            -webkit-text-stroke: 1px #f57f17; filter: drop-shadow(0 0 5px rgba(255, 215, 0, 0.8));
            position: relative; line-height: 1;
        }
        .flame {
            position: absolute; top: -35px; left: 50%; transform: translateX(-50%);
            width: 16px; height: 30px;
            background: radial-gradient(ellipse at bottom, #fff 20%, #ffeb3b 50%, #ff9800 90%, transparent 100%);
            border-radius: 50% 50% 20% 20%;
            box-shadow: 0 0 10px #fff, 0 -5px 20px #ffeb3b, 0 -10px 30px #ff5722;
            animation: flicker 0.1s infinite alternate; transform-origin: bottom center;
        }
        .number-candle::after {
            content: ''; position: absolute; top: -5px; left: 50%; transform: translateX(-50%);
            width: 2px; height: 8px; background: #333;
        }
        @keyframes flicker {
            0% { transform: translateX(-50%) scale(1) skewX(2deg); opacity: 0.9; }
            100% { transform: translateX(-50%) scale(1) skewX(0deg); opacity: 0.9; }
        }

        .guestbook-wrapper {
            margin-top: 15px; background: rgba(0, 0, 0, 0.2); border-radius: 15px;
            padding: 10px; max-height: 400px; overflow-y: auto;
        }
        .toggle-write-btn {
            background: linear-gradient(45deg, #ff758c, #ff7eb3); border: none; color: white;
            padding: 10px 25px; border-radius: 25px; font-size: 0.9rem; font-weight: 600;
            cursor: pointer; margin-top: 25px; margin-bottom: 10px; font-family: 'Poppins';
            transition: all 0.3s; box-shadow: 0 4px 15px rgba(255, 117, 140, 0.4); display: inline-block;
        }
        .toggle-write-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255, 117, 140, 0.6); }
        #write-section { display: none; margin-top: 15px; animation: slideUp 0.4s ease; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <!-- OVERLAY MASUK (Hitam ke Transparan) -->
    <div id="entrance-overlay"></div>

    <!-- Confetti Container -->
    <div id="confetti-container"></div>

    <div class="container" style="max-width: 500px; padding-top: 1rem; padding-bottom: 2rem;"> 
        
        <!-- Kue Realistis -->
        <div class="cake-container">
            <div class="candles">
                <div class="number-candle">2<div class="flame"></div></div>
                <div class="number-candle">0<div class="flame"></div></div>
            </div>
            <div class="cake-top">
                <div class="drips">
                    <div class="drip"></div><div class="drip"></div><div class="drip"></div><div class="drip"></div><div class="drip"></div><div class="drip"></div>
                </div>
            </div>
            <div class="cake-body"></div>
            <div class="plate"></div>
        </div>

        <h1>Selamat Ulang Tahun<br><span style="color: #ff9a9e;"><?= htmlspecialchars($nama_penerima) ?></span></h1>
        
        <p style="font-size: 0.95rem; margin-bottom: 20px; color: #f0f0f0; line-height: 1.6; font-style: italic;">
            "Dua puluh tahun perjalananmu di dunia,<br>
            terima kasih telah tumbuh menjadi jiwa yang begitu indah.<br>
            Semoga semesta senantiasa memelukmu dengan bahagia."
        </p>

        <hr style="border: 0.5px solid rgba(255,255,255,0.1); margin-bottom: 15px;">

        <!-- DAFTAR PESAN -->
        <h3 style="font-family: 'Poppins'; font-size: 1rem; margin-bottom: 10px; text-align:left; padding-left:10px;">📖 Pesan Masuk</h3>
        <div class="guestbook-wrapper">
            <div id="guestbook-list" class="guestbook-container">
                <div style="text-align: center; padding: 20px;">
                    <span style="font-size: 1.5rem;">🌌</span><br>
                    <small>Mengambil pesan dari bintang...</small>
                </div>
            </div>
        </div>

        <!-- TOMBOL TOGGLE TULIS PESAN -->
        <div style="text-align: center;">
            <button class="toggle-write-btn" onclick="toggleWriteForm()">✍️ Tulis Pesan Untuknya</button>
        </div>

        <!-- FORMULIR -->
        <div id="write-section">
            <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px;">
                <p style="font-size: 0.8rem; margin-bottom: 10px; color: #ddd;">Tinggalkan jejak abadi di sini.</p>
                <form id="guestbook-form">
                    <input type="text" name="name" class="input-name" placeholder="Nama Kamu" required maxlength="30" style="font-size: 0.9rem; padding: 8px;">
                    <textarea name="message" id="msg-input" class="input-msg" rows="3" placeholder="Tuliskan doa terbaikmu..." required maxlength="300" style="font-size: 0.9rem; margin-top: 8px;"></textarea>
                    <button type="submit" class="btn-submit" style="width: 100%; margin-top: 10px; padding: 8px; font-size: 0.9rem;">Kirim ❤️</button>
                </form>
            </div>
        </div>

        <a href="ending.php" style="color: #888; font-size: 0.75rem; margin-top: 30px; display: block; text-decoration: none; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 15px;">
            Selesai? Klik untuk penutup manis.
        </a>
    </div>

    <!-- Music Control -->
    <button id="musicControl" style="position: fixed; bottom: 20px; right: 20px; z-index: 999; background: rgba(255, 117, 140, 0.8); color: white; border: none; padding: 8px 12px; border-radius: 50px; cursor: pointer; font-family:'Poppins'; backdrop-filter: blur(5px); font-size: 0.8rem;">🎵 Music</button>
    <audio id="bgm" loop><source src="assets/music/bgm.mp3" type="audio/mpeg"></audio>

    <script src="assets/js/script.js"></script>
    <script>
        const colors = ['#ff9a9e', '#fad0c4', '#a18cd1', '#fbc2eb'];
        setInterval(() => {
            const el = document.createElement('div');
            el.className = 'confetti';
            el.style.left = Math.random() * 100 + 'vw';
            el.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 3000);
        }, 300);

        function toggleWriteForm() {
            const form = document.getElementById('write-section');
            const btn = document.querySelector('.toggle-write-btn');
            
            if (form.style.display === 'block') {
                form.style.display = 'none';
                btn.innerHTML = "✍️ Tulis Pesan Untuknya";
                btn.style.background = "linear-gradient(45deg, #ff758c, #ff7eb3)";
                btn.style.color = "white";
            } else {
                form.style.display = 'block';
                btn.innerHTML = "❌ Tutup Form";
                btn.style.background = "rgba(255, 117, 140, 0.2)";
                btn.style.color = "#ffcae9";
                setTimeout(() => {
                     form.scrollIntoView({behavior: "smooth", block: "center"});
                }, 100);
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            // Restore Music Volume Fade In (Optional)
            const bgm = document.getElementById('bgm');
            if(bgm && localStorage.getItem('musicPlaying') === 'true') {
                 bgm.volume = 0; // Mulai dari 0
                 bgm.play().catch(()=>{});
                 let vol = 0;
                 const fadeIn = setInterval(() => {
                     if(vol < 0.5) {
                         vol += 0.05;
                         bgm.volume = vol;
                     } else {
                         clearInterval(fadeIn);
                     }
                 }, 200);
            }

            const msgForm = document.getElementById('guestbook-form');
            if(msgForm) {
                msgForm.onsubmit = function() {
                    setTimeout(() => {
                        toggleWriteForm(); 
                    }, 500); 
                };
            }
        });
    </script>
</body>
</html>