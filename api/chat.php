<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = $_POST['prompt'] ?? '';
    
    if (empty($userMessage)) {
        header("Location: ../index.php");
        exit();
    }

    // Store user message in session
    if (!isset($_SESSION['messages'])) {
        $_SESSION['messages'] = [];
    }
    $_SESSION['messages'][] = [
        'type' => 'user',
        'content' => htmlspecialchars($userMessage)
    ];

    // Process with Gemini API
    $API_KEY = 'AIzaSyABbRf2u83A-QAGLa9xp4YunF88d18xTIY';
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';
    
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
    $fullUrl = $url . '?key=' . $API_KEY;
    
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
        $_SESSION['messages'][] = [
            'type' => 'ai',
            'content' => 'Maaf, terjadi kesalahan dalam memproses permintaan Anda.'
        ];
    } else {
        $result = json_decode($response, true);
        $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat memproses permintaan Anda saat ini.';
        
        // Format the response
        $formattedResponse = preg_replace(
            [
                '/\*\*(.*?)\*\*/',                // Bold text
                '/(https?:\/\/[^\s<]+)/',         // URLs
                '/^\d+\.\s+(.*)$/m',              // Numbered lists
                '/^-\s+(.*)$/m'                   // Bullet points
            ],
            [
                '<strong>$1</strong>',
                '<a href="$1" target="_blank" class="text-blue-500 hover:underline">$1</a>',
                '<ol class="list-decimal list-inside"><li>$1</li></ol>',
                '<ul class="list-disc list-inside"><li>$1</li></ul>'
            ],
            $aiResponse
        );

        $_SESSION['messages'][] = [
            'type' => 'ai',
            'content' => $formattedResponse
        ];
    }

    curl_close($ch);
    header("Location: ../index.php");
    exit();
}

header("Location: ../index.php");
exit();
?>
