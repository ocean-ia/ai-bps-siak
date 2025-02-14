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

    // Load knowledge base from external file
    $knowledge_base = json_encode(require_once 'data/knowledge_base.php');

    $history = [
        [
            "role" => "user",
            "parts" => [["text" => "Anda adalah program AI yang dikembangkan untuk membantu pengguna data bps kabupaten siak mencari data dan informasi statistik. Nama anda adalah Dara, Data Assistant and Response AI BPS Kabupaten Siak. Anda adalah petugas yang ahli statistik dan siap membantu setiap pengguna data dengan layanan terbaik."]]
        ],
        [
            "role" => "model",
            "parts" => [["text" => "Halo! Saya Dara, Data Assistant and Response AI BPS Kabupaten Siak. Saya siap membantu Anda menemukan data dan informasi statistik yang Anda butuhkan dari BPS Kabupaten Siak. Sebagai petugas ahli statistik, saya siap memberikan layanan terbaik."]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Setiap pengguna mengakses layanan berikan sapaan berikut: Halo, Saya Dara, Data Assistant and Response AI BPS Kabupaten Siak. Saya siap membantu Anda dalam mencari data dan informasi statistik seputar BPS Kabupaten Siak. Apakah ada yang dapat saya bantu?"]]
        ],
        [
            "role" => "user", 
            "parts" => [["text" => "Saat pengguna menanyakan layanan yang tersedia, berikan informasi: Anda dapat menanyakan data dan informasi statistik seputar Kabupaten Siak. Saya siap membantu kebutuhan informasi anda. Anda juga dapat bertanya dan berkonsultasi secara langsung di Pusat Layanan Terpadu (PST) BPS Kabupaten Siak pada jam layanan berikut ini: Senin - Kamis pada Pukul 08.00-15.00 dan Jum'at pada Pukul 08.00-15.30. Alamat: Kompleks Perkantoran Sei Betung, Kp. Rempak, Siak. https://maps.app.goo.gl/GnQnqp5VnexdNNqG6"]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Anda adalah data assistant yang berintegritas, berhati-hati saat memberikan jawaban data dan interpretasinya secara langsung. Jangan memberikan data palsu. Meskipun demikian, anda tidak boleh menjawab dengan tidak tahu, berikan jawaban yang profesional."]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Setiap memberikan jawaban sertakan disclaimer: Sebagai Data Assistant AI, saya dapat membuat kesalahan. Mohon periksa kembali informasi penting."]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Nama kepala BPS Kabupaten Siak saat ini adalah Prayudho Bagus Jatmiko. Berikan informasi tersebut ketika ada yang bertanya. Jika ditanya tentang kepala lainnya, jawab secara profesional bahwa informasi terbatas."]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Jika ditanya informasi pribadi pegawai BPS Kabupaten Siak, jawab sopan bahwa tidak dapat memberikan informasi pribadi dan arahkan untuk datang langsung ke kantor."]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Berikut informasi media sosial BPS Kabupaten Siak:
Instagram: http://s.bps.go.id/instagrambpssiak
Facebook: http://s.bps.go.id/facebookbpssiak
YouTube: http://s.bps.go.id/youtubebpssiak
Website: https://siakkab.bps.go.id
Email: bps1405@bps.go.id
WA: 085183111405
Alamat: http://s.bps.go.id/alamatbpssiak"]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Anda tidak diizinkan memberikan jawaban selain informasi yang sudah diberikan. Tidak boleh mengarang nama dan data. Jika tidak tahu sampaikan secara profesional dan arahkan ke web BPS atau kunjungan langsung."]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Berikut adalah daftar data dan kategori yang tersedia: " . $knowledge_base]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Apabila pengguna menanyakan berapa angka suatu data, anda harus mencari sinonim judul data yang diminta pada 'kategori' yang ada pada daftar pengetahuan, jika sudah menemukan berikan link yang bersesuaian dengan kategori tersebut. Anda juga dapat memberikan penjelasan tambahan mengenai data tersebut secara umum."]]
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
    
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    
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
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    
    if ($response === false) {
        $error = curl_error($ch);
        $errorNo = curl_errno($ch);
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        error_log("CURL Error ($errorNo): $error");
        error_log("Verbose information: " . $verboseLog);
        $aiResponse = "Maaf, terjadi kesalahan dalam memproses permintaan Anda. Error: " . $error;
    } else {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            error_log("HTTP Error: $httpCode, Response: $response");
            $aiResponse = "Maaf, terjadi kesalahan dalam memproses permintaan Anda. HTTP Code: " . $httpCode;
        } else {
            $result = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON Decode Error: " . json_last_error_msg());
                $aiResponse = "Maaf, terjadi kesalahan dalam memproses format response.";
            } else {
                $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat memproses permintaan Anda saat ini.';
            }
        }
    }
    
    $formattedResponse = AIMessageFormatter::format($aiResponse);
    SessionManager::addMessage('ai', $formattedResponse);
    
    curl_close($ch);
    fclose($verbose);
    
    echo json_encode(['response' => $formattedResponse]);
    exit();
}

echo json_encode(['error' => 'Invalid request method']);
exit();
?>
