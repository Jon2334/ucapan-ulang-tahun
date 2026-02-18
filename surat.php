<?php 
// Session dihapus agar kompatibel dengan Vercel Serverless
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sebuah Pesan Untukmu</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Override/Additions specific to surat.php */
        body {
            overflow: hidden; /* Prevent scroll during animation */
        }

        /* --- Overlay Transisi Keluar --- */
        #transition-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: black;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 2s ease-in-out; /* Durasi fade out 2 detik */
        }
        #transition-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        .envelope-wrapper {
            position: relative;
            cursor: pointer;
            animation: float 4s ease-in-out infinite;
            margin-top: 50px;
        }

        .envelope {
            position: relative;
            width: 300px;
            height: 200px;
            background: #e74c3c;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        /* Lidah Amplop (Flap) */
        .envelope:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
            border-left: 150px solid transparent;
            border-right: 150px solid transparent;
            border-top: 130px solid #c0392b;
            transform-origin: top;
            transition: transform 0.6s ease, z-index 0.6s;
            z-index: 5;
        }

        .envelope.open:before {
            transform: rotateX(180deg);
            z-index: 1;
        }

        /* Sisi Kiri Kanan Amplop */
        .envelope:after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
            border-left: 150px solid #e74c3c;
            border-right: 150px solid #e74c3c;
            border-bottom: 100px solid #c0392b;
            border-top: 100px solid transparent;
            border-radius: 0 0 10px 10px;
            z-index: 4;
            pointer-events: none;
        }

        /* Segel Lilin */
        .wax-seal {
            position: absolute;
            top: 60px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 40px;
            background: #f1c40f;
            border-radius: 50%;
            border: 2px solid #d4ac0d;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            z-index: 6;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            transition: opacity 0.4s;
        }
        
        .envelope.open .wax-seal {
            opacity: 0;
        }

        /* Kertas Surat */
        .paper {
            position: absolute;
            top: 10px;
            left: 15px;
            right: 15px;
            height: 180px;
            background: #fff;
            border-radius: 5px;
            transition: transform 0.8s ease 0.4s;
            z-index: 2;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        .envelope.open .paper {
            transform: translateY(-120px);
            z-index: 3;
        }
        
        .paper-preview {
            width: 80%;
            height: 10px;
            background: #eee;
            border-radius: 2px;
            box-shadow: 0 15px 0 #eee, 0 30px 0 #eee;
        }

        /* --- Full Screen Reader Modal --- */
        .reading-mode {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 100;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.8s;
            backdrop-filter: blur(5px);
        }

        .reading-mode.active {
            opacity: 1;
            pointer-events: all;
        }

        .letter-paper {
            width: 90%;
            max-width: 500px;
            min-height: 60vh;
            background: #fffdf0;
            padding: 40px;
            border-radius: 5px;
            box-shadow: 0 0 30px rgba(255,255,255,0.15);
            position: relative;
            transform: scale(0.8);
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background-image: linear-gradient(#999 1px, transparent 1px);
            background-size: 100% 1.5rem;
            line-height: 1.5rem;
        }

        .reading-mode.active .letter-paper {
            transform: scale(1);
        }

        .typing-container {
            font-family: 'Dancing Script', cursive;
            font-size: 1.6rem;
            color: #444;
            line-height: 1.5rem;
            text-align: left;
            min-height: 200px;
            margin-top: 4px;
        }
        
        .btn-continue {
            margin-top: 30px;
            background: #ff758c;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            font-size: 1rem;
            opacity: 0;
            transition: opacity 1s;
            box-shadow: 0 4px 15px rgba(255, 117, 140, 0.4);
        }
        .btn-continue:hover {
            background: #ff5c77;
            transform: scale(1.05);
        }

        .btn-continue.show {
            opacity: 1;
        }
    </style>
</head>
<body>

    <!-- Stars Background -->
    <script>
        for(let i=0; i<60; i++) {
            let s = document.createElement('div');
            s.className = 'star';
            s.style.left = Math.random()*100 + 'vw';
            s.style.top = Math.random()*100 + 'vh';
            s.style.animationDuration = (Math.random()*3 + 1) + 's';
            document.body.appendChild(s);
        }
    </script>

    <!-- Overlay Hitam untuk Transisi -->
    <div id="transition-overlay"></div>

    <h2 style="margin-bottom: 40px; font-family:'Poppins'; font-weight:300; opacity:0.9; text-shadow: 0 2px 5px rgba(0,0,0,0.5);">Sebuah pesan rahasia...</h2>

    <div class="envelope-wrapper" onclick="openLetter()">
        <div class="envelope">
            <div class="paper">
                <div class="paper-preview"></div>
            </div>
            <div class="wax-seal">❤️</div>
        </div>
    </div>
    
    <p style="margin-top: 50px; font-size: 0.9rem; opacity: 0.6; font-family:'Poppins'">(Ketuk amplop untuk membuka)</p>

    <!-- Reading Mode Overlay -->
    <div class="reading-mode" id="reading-mode">
        <div class="letter-paper">
            <div class="typing-container" id="letter-text"></div>
            <div style="text-align: center; background:transparent; margin-top:2rem;">
                <button class="btn-continue" id="btn-next" onclick="goToNext()">Simpan dalam hati & Lanjut ❤️</button>
            </div>
        </div>
    </div>

    <!-- Audio -->
    <audio id="bgm" loop>
        <source src="assets/music/bgm.mp3" type="audio/mpeg">
    </audio>

    <script src="assets/js/script.js"></script>
    <script>
        const bgm = document.getElementById('bgm');
        if(localStorage.getItem('musicPlaying') === 'true') {
            bgm.volume = 0.5;
            bgm.play().catch(()=>{});
        }

        let isOpened = false;

        function openLetter() {
            if(isOpened) return;
            isOpened = true;
            const envelope = document.querySelector('.envelope');
            envelope.classList.add('open');
            setTimeout(() => {
                document.getElementById('reading-mode').classList.add('active');
                setTimeout(startTyping, 500);
            }, 1200);
        }

        const message = `Hai...
        
        Selamat ulang tahun.
        
        Mungkin ini sederhana, tapi percayalah,
        doa yang terselip di dalamnya begitu besar.
        Terima kasih telah bertahan sejauh ini.
        
        Dunia menjadi lebih baik karena kehadiranmu.
        Tetaplah bersinar, tetaplah menginspirasi.
        
        Aku selalu bangga padamu.`;

        function startTyping() {
            const container = document.getElementById('letter-text');
            const lines = message.split('\n');
            let lineIndex = 0;
            let charIndex = 0;

            function type() {
                if (lineIndex < lines.length) {
                    const currentLine = lines[lineIndex];
                    if (charIndex < currentLine.length) {
                        let char = currentLine.charAt(charIndex);
                        container.innerHTML += char;
                        charIndex++;
                        setTimeout(type, Math.random() * 50 + 30); 
                    } else {
                        container.innerHTML += '<br>';
                        lineIndex++;
                        charIndex = 0;
                        setTimeout(type, 300); 
                    }
                } else {
                    document.getElementById('btn-next').classList.add('show');
                }
            }
            type();
        }

        // --- FUNGSI TRANSISI ANIMASI ---
        function goToNext() {
            const overlay = document.getElementById('transition-overlay');
            
            // 1. Fade Out Music (Audio mengecil perlahan)
            if(bgm && !bgm.paused) {
                let vol = bgm.volume;
                const fadeAudio = setInterval(() => {
                    if (vol > 0.05) {
                        vol -= 0.05;
                        bgm.volume = vol;
                    } else {
                        clearInterval(fadeAudio);
                    }
                }, 100);
            }

            // 2. Aktifkan Layar Hitam
            overlay.classList.add('active');

            // 3. Redirect setelah 2 detik (saat layar sudah gelap total)
            setTimeout(() => {
                window.location.href = 'ucapan.php';
            }, 2000);
        }
    </script>
</body>
</html>