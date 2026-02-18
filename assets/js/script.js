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

            // Coba putar audio
            const playPromise = bgm.play();
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.log("Autoplay dicegah oleh browser, menunggu interaksi user.");
                });
            }
        }
    }

    // Toggle Button Musik (ON/OFF)
    if(musicBtn) {
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

    // PENTING: Simpan posisi lagu saat ini sebelum pindah halaman
    window.addEventListener('beforeunload', () => {
        if(bgm && !bgm.paused) {
            localStorage.setItem('audioTime', bgm.currentTime);
            localStorage.setItem('musicPlaying', 'true');
        }
    });

    // 2. LOAD GUESTBOOK MESSAGES (Hanya di ucapan.php)
    const guestbookList = document.getElementById('guestbook-list');
    if(guestbookList) {
        loadMessages();
        setInterval(loadMessages, 5000); // Auto refresh setiap 5 detik
    }

    // 3. HANDLE FORM SUBMIT (AJAX)
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

            fetch('save_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    document.getElementById('msg-input').value = ''; // Reset input
                    loadMessages(); // Refresh list
                    
                    // Pindah ke tab baca pesan (jika fungsi ada)
                    if(typeof switchTab === 'function') {
                        switchTab('read');
                    }
                } else {
                    alert('Gagal mengirim: ' + data.message);
                }
            })
            .catch(err => console.error(err))
            .finally(() => {
                btn.innerText = originalText;
                btn.disabled = false;
            });
        });
    }
    
    // 4. ANIMASI BINTANG (Background)
    createStars();
});

// Fungsi Load Pesan
function loadMessages() {
    fetch('get_messages.php')
    .then(response => response.json())
    .then(data => {
        const list = document.getElementById('guestbook-list');
        if(!list) return;

        list.innerHTML = '';
        if(data.length === 0) {
            list.innerHTML = '<div style="text-align:center; padding:20px; color:#ddd;">Belum ada ucapan. Jadilah yang pertama!</div>';
        } else {
            data.forEach(msg => {
                const item = document.createElement('div');
                item.className = 'message-card fade-in';
                const safeName = msg.name.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                const safeMsg = msg.message.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                
                item.innerHTML = `
                    <div class="msg-sender">
                        <span style="color:#ffeb3b">★</span> ${safeName} 
                        <span style="float:right; font-size:0.7rem; color:#ccc; font-weight:normal;">${msg.date}</span>
                    </div>
                    <div class="msg-content">${safeMsg}</div>
                `;
                list.appendChild(item);
            });
        }
    })
    .catch(err => console.error("Gagal memuat pesan:", err));
}

// Fungsi Bikin Bintang
function createStars() {
    const body = document.body;
    for(let i=0; i<50; i++) { // Jumlah bintang
        let star = document.createElement('div');
        star.className = 'star';
        star.style.left = Math.random() * 100 + 'vw';
        star.style.top = Math.random() * 100 + 'vh';
        star.style.animationDuration = (Math.random() * 3 + 2) + 's';
        body.appendChild(star);
    }
}