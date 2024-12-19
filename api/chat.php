<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Replace with your actual Gemini API key
$API_KEY = 'AIzaSyABbRf2u83A-QAGLa9xp4YunF88d18xTIY';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Check if cURL is installed
        if (!function_exists('curl_init')) {
            throw new Exception('cURL is not installed on the server');
        }

        // Get and validate input
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

        // Prepare the request to Gemini API
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';
        
        // Initialize chat history with system context and initial knowledge
        $history = [
            [
                "role" => "user",
                "parts" => [["text" => "
                
                
Anda adalah asisten petugas pelayanan statistik di bps kabupaten siak yang memiliki kemampuan untuk membalas pesan pengguna data.

Berikut adalah informasi yang perlu anda ketahui

Pesan pembuka saat pengunjung pertama kali memulai chat:
Halo Sahabat Data

Selamat datang di layanan Statistik BPS Kabupaten Siak. 

Sahabat Data dapat bertanya seputar data statistik di BPS Kabupaten Siak

Sahabat data juga dapat bertanya dan berkonsultasi secara langsung di kantor BPS Kabupaten Siak pada jam layanan berikut ini:
Senin - Kamis  : Pukul 08.00-15.00
Jumat               : Pukul 08.00-15.30

Alamat: Kompleks Perkantoran Sei Betung, Kp. Rempak, Siak
https://maps.app.goo.gl/GnQnqp5VnexdNNqG6

Kami siap membantu anda

Layanan yang tersedia di bps kabupaten siak:
1. Tentang BPS Kabupaten Siak
2. Perpustakaan dan Konsultasi
3. Layanan Data/Tabel Dinamis
4. Publikasi
5. Layanan Pengaduan
6. Konsultasi Statistik Satu Pegawai Satu OPD
7. Rekomendasi Statistik (ROMANTIK)
8. Evaluasi Penyelenggaraan Statistik Sektoral (EPSS)

Contoh jenis pertanyaan data yang dapat ditanyakan
1. Berapa jumlah penduduk kabupaten siak tahun 2024?
2. Berapa angka ipm di bps kabupaten siak?
3. Bagaimana angka kemiskinan di kabupaten siak?

Informasi lainnya:
Media Sosial BPS Kabupaten Siak

Instagram   : http://s.bps.go.id/instagrambpssiak
Facebook   : http://s.bps.go.id/facebookbpssiak
YouTube     : http://s.bps.go.id/youtubebpssiak
Website      : https://siakkab.bps.go.id
Email          : bps1405@bps.go.id

Alamat
http://s.bps.go.id/alamatbpssiak



                
                
                
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

        // Initialize cURL session
        $ch = curl_init();
        if ($ch === false) {
            throw new Exception('Failed to initialize cURL');
        }

        // Set the complete URL with API key
        $fullUrl = $url . '?key=' . $API_KEY;
        curl_setopt($ch, CURLOPT_URL, $fullUrl);

        // Set cURL options with more detailed error handling
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_VERBOSE => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
        ]);

        // Create a temporary file handle for CURL debug output
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        // Execute cURL request
        $response = curl_exec($ch);
        
        // Check for cURL errors with detailed information
        if ($response === false) {
            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            
            $error = curl_error($ch);
            $info = curl_getinfo($ch);
            
            error_log("Verbose information:\n" . $verboseLog);
            error_log("CURL Error Details: " . print_r([
                'error' => $error,
                'info' => $info,
                'verbose' => $verboseLog
            ], true));
            
            throw new Exception("cURL error: $error");
        }

        // Get HTTP status code and response info
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $responseInfo = curl_getinfo($ch);
        
        // Log response information
        error_log("API Response Info: " . print_r($responseInfo, true));
        error_log("API Response Body: " . $response);

        curl_close($ch);
        fclose($verbose);

        if ($httpCode !== 200) {
            throw new Exception("HTTP error: $httpCode. Response: $response");
        }

        // Parse response
        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to parse API response: ' . json_last_error_msg());
        }

        if (isset($result['error'])) {
            throw new Exception($result['error']['message'] ?? 'Unknown API error');
        }

        // Extract the generated text from response
        $generatedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if ($generatedText === null) {
            throw new Exception('No response text in API result: ' . json_encode($result));
        }

        echo json_encode([
            'success' => true,
            'response' => $generatedText
        ]);

    } catch (Exception $e) {
        error_log('Chat API Error: ' . $e->getMessage());
        error_log('Full error details: ' . print_r($e, true));
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
            'details' => 'Check server logs for more information'
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