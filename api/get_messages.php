<?php
/**
 * JonKrisBot - API untuk mengambil pesan dari Firebase Realtime Database
 * Disesuaikan untuk deployment di Vercel (Serverless)
 */

// Izinkan akses dari mana saja (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Coba ambil URL dari Environment Variable Vercel (Sangat disarankan set di Dashboard Vercel)
$firebaseUrl = getenv('FIREBASE_URL');

// Jika tidak ada ENV, gunakan URL hardcode dengan suffix .json
if (!$firebaseUrl) {
    // Menambahkan 'messages.json' di akhir URL agar REST API Firebase bekerja
    $firebaseUrl = 'https://ucapan-ultah-87b51-default-rtdb.asia-southeast1.firebasedatabase.app/messages.json';
}

// Inisialisasi cURL untuk mengambil data (GET request)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Skip validasi SSL untuk kelancaran di lingkungan serverless
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

$response = curl_exec($ch);

// Cek jika terjadi error pada cURL
if (curl_errno($ch)) {
    echo json_encode(["status" => "error", "message" => curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Jika database kosong, Firebase mengembalikan string 'null'
if ($response === 'null' || empty($response)) {
    echo json_encode([]);
    exit;
}

/**
 * Firebase mengembalikan data dalam format Object dengan Key unik (ID generate otomatis)
 * Contoh: {"-Mxz123": {"nama": "A", "pesan": "B"}, "-Mxy456": {...}}
 * Frontend biasanya membutuhkan format Array [] agar mudah di-loop.
 */
$data = json_decode($response, true);
$formattedData = [];

if (is_array($data)) {
    foreach ($data as $key => $item) {
        // Masukkan data ke array baru
        $formattedData[] = $item;
    }

    // Urutkan dari yang terbaru (karena default Firebase adalah urutan masuk)
    $formattedData = array_reverse($formattedData);
}

// Kembalikan JSON ke frontend
echo json_encode($formattedData);
?>