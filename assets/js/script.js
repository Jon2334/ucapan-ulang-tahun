document.addEventListener("DOMContentLoaded", () => {
    // 1. SETUP AUDIO PLAYER
    const bgm = document.getElementById('bgm');
    const musicBtn = document.getElementById('musicControl');
    
    if(bgm) {
        bgm.volume = 0.5;

        // Cek apakah musik harus diputar (dari localStorage)
        if(localStorage.getItem('musicPlaying') === 'true') {
            // FITUR RESUME: Ambil waktu terakhir lagu
            const savedTime = localStorage.getItem('audioTime');
            if(savedTime) {
                bgm.currentTime = parseFloat(savedTime);
            }

            // Coba putar audio (Autoplay handle)
            const playPromise = bgm.play();
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.log("Autoplay dicegah browser, menunggu interaksi.");
                    // Update UI button jika autoplay gagal
                    if(musicBtn) musicBtn.innerText = "🔇 OFF";
                });
            }
        }
    }

    // Toggle Button Musik (ON/OFF)
    if(musicBtn) {
        // Set label awal sesuai state
        musicBtn.innerText = (bgm && !bgm.paused) ? "🎵 ON" : "🔇 OFF";

        musicBtn.addEventListener('click', () => {
            if (bgm.paused) {
                bgm.play();
                musicBtn.innerText = "🎵 ON";
                localStorage.setItem('musicPlaying', 'true');
            } else {
                bgm.pause();
                musicBtn.innerText = "🔇 OFF";
                localStorage.setItem('musicPlaying', 'false');
            }
        });
    }

    // PENTING: Simpan posisi lagu saat ini sebelum pindah halaman/refresh
    window.addEventListener('beforeunload', () => {
        if(bgm) {
            localStorage.setItem('audioTime', bgm.currentTime);
            localStorage.setItem('musicPlaying', !bgm.paused);
        }
    });

    // 2. LOAD GUESTBOOK MESSAGES (Hanya di ucapan.php)
    const guestbookList = document.getElementById('guestbook-list');
    if(guestbookList) {
        loadMessages();
        // Auto refresh setiap 10 detik agar tidak membebani limit gratis Firebase
        setInterval(loadMessages, 10000); 
    }

    // 3. HANDLE FORM SUBMIT (AJAX ke API)
    const msgForm = document.getElementById('guestbook-form');
    if(msgForm) {
        msgForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerText;
            
            // UI Loading
            btn.innerText = "Mengirim...";
            btn.disabled = true;

            const formData = new FormData(this);
            const payload = Object.fromEntries(formData.entries());

            // Kirim ke api/save_message.php
            fetch('api/save_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    this.reset(); // Reset form
                    loadMessages(); // Refresh list ucapan
                    
                    // Jika ada fungsi switchTab untuk pindah tampilan
                    if(typeof switchTab === 'function') {
                        switchTab('read');
                    }
                } else {
                    alert('Gagal: ' + data.message);
                }
            })
            .catch(err => {
                console.error("Error saving message:", err);
                alert('Terjadi kesalahan koneksi.');
            })
            .finally(() => {
                btn.innerText = originalText;
                btn.disabled = false;
            });
        });
    }
    
    // 4. ANIMASI BINTANG (Background)
    createStars();
});

/**
 * Fungsi Mengambil Pesan dari Database via PHP Proxy
 */
function loadMessages() {
    const list = document.getElementById('guestbook-list');
    if(!list) return;

    fetch('api/get_messages.php')
    .then(response => response.json())
    .then(data => {
        list.innerHTML = '';
        
        if(!data || data.length === 0) {
            list.innerHTML = '<div style="text-align:center; padding:20px; color:#ddd;">Belum ada ucapan. Yuk, jadi yang pertama!</div>';
            return;
        }

        data.forEach(msg => {
            const item = document.createElement('div');
            item.className = 'message-card fade-in';
            
            // Sanitasi tambahan di frontend (Double Protection)
            const safeName = (msg.nama || 'Anonim').replace(/</g, "&lt;").replace(/>/g, "&gt;");
            const safeMsg = (msg.pesan || '').replace(/</g, "&lt;").replace(/>/g, "&gt;");
            const safeDate = msg.waktu || '';
            
            item.innerHTML = `
                <div class="msg-sender">
                    <span style="color:#ffeb3b">★</span> ${safeName} 
                    <span style="float:right; font-size:0.7rem; color:#ccc; font-weight:normal;">${safeDate}</span>
                </div>
                <div class="msg-content">${safeMsg}</div>
            `;
            list.appendChild(item);
        });
    })
    .catch(err => {
        console.error("Gagal memuat pesan:", err);
        list.innerHTML = '<div style="text-align:center; color:red;">Gagal memuat pesan...</div>';
    });
}

/**
 * Fungsi Membuat Animasi Bintang di Background
 */
function createStars() {
    const body = document.body;
    // Cek agar tidak menduplikasi bintang jika fungsi terpanggil ulang
    if(document.querySelectorAll('.star').length > 0) return;

    for(let i=0; i<50; i++) {
        let star = document.createElement('div');
        star.className = 'star';
        star.style.left = Math.random() * 100 + 'vw';
        star.style.top = Math.random() * 100 + 'vh';
        star.style.animationDuration = (Math.random() * 3 + 2) + 's';
        star.style.opacity = Math.random();
        body.appendChild(star);
    }
}