<?php
header('Content-Type: application/json');

// 1. Cek Metode Request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

// 2. Ambil & Sanitasi Input (Keamanan Wajib!)
$name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : 'Anonim';
$message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';

// Validasi sederhana
if (empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Pesan tidak boleh kosong']);
    exit;
}

// 3. Persiapkan Data Baru
$newData = [
    'name' => $name,
    'message' => $message,
    'date' => date('d M Y H:i')
];

// 4. Handle File JSON
$file = 'assets/data/messages.json';

// Cek folder exists
if (!file_exists('assets/data')) {
    mkdir('assets/data', 0777, true);
}

// Ambil data lama
$currentData = [];
if (file_exists($file)) {
    $jsonContent = file_get_contents($file);
    $currentData = json_decode($jsonContent, true) ?? [];
}

// Tambahkan data baru di awal array (unshift) agar tampil paling atas
array_unshift($currentData, $newData);

// Batasi hanya menyimpan 50 pesan terakhir agar file tidak terlalu besar
$currentData = array_slice($currentData, 0, 50);

// Simpan kembali ke file
if (file_put_contents($file, json_encode($currentData, JSON_PRETTY_PRINT))) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menulis file']);
}
?>