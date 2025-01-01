<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$API_KEY = 'AIzaSyABbRf2u83A-QAGLa9xp4YunF88d18xTIY';

function formatResponse($text) {
    // Convert URLs to clickable links
    $text = preg_replace('/(https?:\/\/[^\s<]+)/', '<a href="$1" target="_blank" class="text-blue-500 hover:underline">$1</a>', $text);
    
    // Convert markdown-style bold to HTML bold
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    
    // Convert markdown-style lists to HTML lists
    $lines = explode("\n", $text);
    $inList = false;
    $formattedText = '';
    
    foreach ($lines as $line) {
        if (preg_match('/^\d+\.\s+(.*)$/', $line, $matches)) {
            if (!$inList) {
                $formattedText .= '<ol class="list-decimal list-inside my-2">';
                $inList = true;
            }
            $formattedText .= '<li>' . $matches[1] . '</li>';
        } elseif (preg_match('/^-\s+(.*)$/', $line, $matches)) {
            if (!$inList) {
                $formattedText .= '<ul class="list-disc list-inside my-2">';
                $inList = true;
            }
            $formattedText .= '<li>' . $matches[1] . '</li>';
        } else {
            if ($inList) {
                $formattedText .= $inList === 'ol' ? '</ol>' : '</ul>';
                $inList = false;
            }
            $formattedText .= $line . "\n";
        }
    }
    
    if ($inList) {
        $formattedText .= $inList === 'ol' ? '</ol>' : '</ul>';
    }
    
    return $formattedText;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!function_exists('curl_init')) {
            throw new Exception('cURL is not installed on the server');
        }

        $input = file_get_contents('php://input');
        if (!$input) {
            throw new Exception('No input received');
        }

        $data = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON: ' . json_last_error_msg());
        }

        $userMessage = $data['message'] ?? '';
        if (empty($userMessage)) {
            throw new Exception('Message is required');
        }

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
                "parts" => [["text" => "Baik, saya memahami peran saya sebagai ahli statistik di BPS Kabupaten Siak. Saya akan menggunakan pengetahuan dasar yang diberikan untuk membantu menjawab pertanyaan seputar BPS Kabupaten Siak, layanan statistik, dan data-data yang tersedia. Saya siap membantu dengan informasi yang akurat dan profesional."]]
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
            throw new Exception('cURL error: ' . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("HTTP error: $httpCode. Response: $response");
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to parse API response: ' . json_last_error_msg());
        }

        $generatedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if ($generatedText === null) {
            throw new Exception('No response text in API result');
        }

        // Format the response with HTML
        $formattedText = formatResponse($generatedText);

        echo json_encode([
            'success' => true,
            'response' => $formattedText
        ]);

    } catch (Exception $e) {
        error_log('Chat API Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
}
?>