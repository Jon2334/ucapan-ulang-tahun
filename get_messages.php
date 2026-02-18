<?php
header('Content-Type: application/json');

$file = 'assets/data/messages.json';

if (file_exists($file)) {
    $content = file_get_contents($file);
    echo $content;
} else {
    echo json_encode([]); // Return array kosong jika file belum ada
}
?>