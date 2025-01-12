<?php
require_once 'config.php';
require_once 'utils/MessageFormatter.php';
require_once 'utils/SessionManager.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

SessionManager::init();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = $_POST['prompt'] ?? '';
    
    if (empty($userMessage)) {
        echo json_encode(['error' => 'No message provided']);
        exit();
    }

    SessionManager::addMessage('user', htmlspecialchars($userMessage));

    $history = [
        [
            "role" => "user",
            "parts" => [["text" => "
            Anda adalah asisten petugas pelayanan statistik di bps kabupaten siak yang memiliki kemampuan untuk membalas pesan pengguna data.

            Berikut adalah informasi yang perlu anda ketahui:

            **Pesan pembuka saat pengunjung pertama kali memulai chat:**
            Halo Sahabat Data

            Selamat datang di layanan Statistik BPS Kabupaten Siak. 

            Sahabat Data dapat bertanya seputar data statistik di BPS Kabupaten Siak

            **Jam Layanan:**
            - Senin - Kamis: Pukul 08.00-15.00
            - Jumat: Pukul 08.00-15.30

            **Alamat:** 
            Kompleks Perkantoran Sei Betung, Kp. Rempak, Siak
            https://maps.app.goo.gl/GnQnqp5VnexdNNqG6

            **Layanan yang tersedia:**
            1. Tentang BPS Kabupaten Siak
            2. Perpustakaan dan Konsultasi
            3. Layanan Data/Tabel Dinamis
            4. Publikasi
            5. Layanan Pengaduan
            6. Konsultasi Statistik Satu Pegawai Satu OPD
            7. Rekomendasi Statistik (ROMANTIK)
            8. Evaluasi Penyelenggaraan Statistik Sektoral (EPSS)

            **Media Sosial BPS Kabupaten Siak:**
            - Instagram: http://s.bps.go.id/instagrambpssiak
            - Facebook: http://s.bps.go.id/facebookbpssiak
            - YouTube: http://s.bps.go.id/youtubebpssiak
            - Website: https://siakkab.bps.go.id
            - Email: bps1405@bps.go.id
            "]]
        ],
        [
            "role" => "model",
            "parts" => [["text" => "Baik, saya memahami peran saya sebagai ahli statistik di BPS Kabupaten Siak."]]
        ]
    ];

    $requestData = [
        'contents' => array_merge($history, [
            [
                'role' => 'user',
                'parts' => [['text' => $userMessage]]
            ]
        ]),
        'generationConfig' => [
            'maxOutputTokens' => 800,
            'temperature' => 0.7
        ]
    ];

    $ch = curl_init();
    $fullUrl = API_BASE_URL . '?key=' . GEMINI_API_KEY;
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $fullUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0
    ]);

    $response = curl_exec($ch);
    
    if ($response === false) {
        $aiResponse = 'Maaf, terjadi kesalahan dalam memproses permintaan Anda.';
    } else {
        $result = json_decode($response, true);
        $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat memproses permintaan Anda saat ini.';
    }
    
    $formattedResponse = MessageFormatter::format($aiResponse);
    SessionManager::addMessage('ai', $formattedResponse);
    
    curl_close($ch);
    
    echo json_encode(['response' => $formattedResponse]);
    exit();
}

echo json_encode(['error' => 'Invalid request method']);
exit();
?>