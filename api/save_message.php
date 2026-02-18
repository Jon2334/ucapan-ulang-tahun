<?php
// Header CORS agar bisa di-POST dari frontend
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight request (OPTIONS) dari browser modern
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Coba ambil URL dari Environment Variable
$firebaseUrl = getenv('FIREBASE_URL');

// Fallback ke URL hardcode jika ENV tidak terbaca
if (!$firebaseUrl) {
    $firebaseUrl = 'https://ucapan-ultah-87b51-default-rtdb.asia-southeast1.firebasedatabase.app/messages.json';
}

// Hanya proses jika method adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Ambil raw input (JSON) dari fetch javascript
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    // Fallback ke $_POST biasa jika form dikirim submit standar (bukan JSON)
    if (is_null($input)) {
        $input = $_POST;
    }

    $nama = isset($input['nama']) ? trim($input['nama']) : '';
    $pesan = isset($input['pesan']) ? trim($input['pesan']) : '';

    // Validasi input tidak boleh kosong
    if (!empty($nama) && !empty($pesan)) {
        
        // 🛡️ SECURITY: Sanitasi input user agar anti-XSS
        $namaAman = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
        $pesanAman = htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8');
        
        // Set zona waktu ke WIB (Jakarta)
        date_default_timezone_set('Asia/Jakarta');

        // Struktur data yang akan disimpan
        $newData = [
            'nama' => $namaAman,
            'pesan' => $pesanAman,
            'waktu' => date('d M Y, H:i') 
        ];

        // Kirim ke Firebase via cURL (POST method agar digenerate key unik otomatis)
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($newData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        
        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            // Jika koneksi ke Firebase gagal
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Gagal koneksi ke database: " . curl_error($ch)]);
        } else {
            // Berhasil
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Ucapan berhasil dikirim!"]);
        }
        
        curl_close($ch);
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Nama dan Pesan wajib diisi!"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Metode request tidak diizinkan"]);
}
?>