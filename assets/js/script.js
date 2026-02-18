document.addEventListener("DOMContentLoaded", () => {
    // === 1. SETUP AUDIO PLAYER ===
    const bgm = document.getElementById('bgm');
    const musicBtn = document.getElementById('musicControl');
    
    if(bgm) {
        bgm.volume = 0.5;
        if(localStorage.getItem('musicPlaying') === 'true') {
            const savedTime = localStorage.getItem('audioTime');
            if(savedTime) bgm.currentTime = parseFloat(savedTime);
            
            const playPromise = bgm.play();
            if (playPromise !== undefined) {
                playPromise.catch(() => {
                    console.log("Autoplay blocked. User interaction required.");
                    if(musicBtn) musicBtn.innerText = "🔇 OFF";
                });
            }
        }
    }

    if(musicBtn) {
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

    window.addEventListener('beforeunload', () => {
        if(bgm) {
            localStorage.setItem('audioTime', bgm.currentTime);
            localStorage.setItem('musicPlaying', !bgm.paused);
        }
    });

    // === 2. GUESTBOOK LOGIC ===
    const guestbookList = document.getElementById('guestbook-list');
    if(guestbookList) {
        loadMessages();
        setInterval(loadMessages, 10000); 
    }

    const msgForm = document.getElementById('guestbook-form');
    if(msgForm) {
        msgForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerText;
            
            btn.innerText = "Mengirim...";
            btn.disabled = true;

            const formData = new FormData(this);
            const payload = Object.fromEntries(formData.entries());

            // FIX: Menggunakan path root karena file save_message.php ada di root
            fetch('save_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(async response => {
                const text = await response.text();
                if (!response.ok) {
                    if (response.status === 403) throw new Error("Akses Ditolak (403). Pastikan vercel.json sudah benar.");
                    throw new Error(`Server Error: ${response.status}`);
                }
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error("Respon server bukan format JSON yang valid.");
                }
            })
            .then(data => {
                if(data.status === 'success') {
                    this.reset();
                    loadMessages();
                    if(typeof switchTab === 'function') switchTab('read');
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(err => {
                console.error("Save Error:", err);
                alert(err.message);
            })
            .finally(() => {
                btn.innerText = originalText;
                btn.disabled = false;
            });
        });
    }
    
    createStars();
});

function loadMessages() {
    const list = document.getElementById('guestbook-list');
    if(!list) return;

    // FIX: Menggunakan path root karena file get_messages.php ada di root
    fetch(`get_messages.php?t=${Date.now()}`)
    .then(async response => {
        if (!response.ok) {
            if(response.status === 403) throw new Error("Akses Ditolak (403). Cek konfigurasi Vercel.");
            throw new Error(`HTTP Error! Status: ${response.status}`);
        }
        return await response.json();
    })
    .then(data => {
        list.innerHTML = '';
        if(!data || data.length === 0) {
            list.innerHTML = '<div style="text-align:center; padding:20px; color:#ddd;">Belum ada ucapan.</div>';
            return;
        }

        data.forEach(msg => {
            const item = document.createElement('div');
            item.className = 'message-card fade-in';
            // Menyesuaikan mapping key (nama & pesan) sesuai file PHP terbaru
            const safeName = (msg.nama || 'Anonim').replace(/</g, "&lt;");
            const safeMsg = (msg.pesan || '').replace(/</g, "&lt;");
            
            item.innerHTML = `
                <div class="msg-sender">
                    <span style="color:#ffeb3b">★</span> ${safeName} 
                    <span style="float:right; font-size:0.7rem; color:#ccc;">${msg.waktu || ''}</span>
                </div>
                <div class="msg-content">${safeMsg}</div>
            `;
            list.appendChild(item);
        });
    })
    .catch(err => {
        console.error("Load Error:", err);
        list.innerHTML = `<div style="text-align:center; color:#ff5252; font-size:0.8rem; padding:10px;">${err.message}</div>`;
    });
}

function createStars() {
    if(document.querySelectorAll('.star').length > 0) return;
    const body = document.body;
    for(let i=0; i<50; i++) {
        let star = document.createElement('div');
        star.className = 'star';
        star.style.left = Math.random() * 100 + 'vw';
        star.style.top = Math.random() * 100 + 'vh';
        star.style.animationDuration = (Math.random() * 3 + 2) + 's';
        body.appendChild(star);
    }
}