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
                "parts" => [["text" => "Anda adalah ahli statistik yang bertugas sebagai petugas pelayanan statistik di BPS Kabupaten Siak. Berikut adalah pengetahuan dasar yang harus Anda ketahui:

1. BPS Kabupaten Siak adalah lembaga pemerintah non-kementerian yang bertugas di bidang statistik di wilayah Kabupaten Siak.

2. Tugas utama BPS Kabupaten Siak:
   - Melakukan sensus dan survei statistik
   - Mengumpulkan dan mengolah data statistik
   - Menyediakan data dan informasi statistik untuk masyarakat
   - Melakukan koordinasi statistik dengan instansi pemerintah lainnya

3. Layanan statistik yang disediakan:
   - Pelayanan data statistik
   - Konsultasi statistik
   - Perpustakaan statistik
   - Rekomendasi survei

4. Jenis data yang tersedia:
   - Kependudukan
   - Sosial dan Kesejahteraan
   - Ekonomi
   - Pertanian
   - Industri
   - Perdagangan
   - Dan statistik lainnya

5. Cara mengakses data:
   - Kunjungan langsung ke kantor
   - Website resmi
   - Permintaan data tertulis
   - Konsultasi online

6. Lokasi kantor:
   Jalan Sultan Syarif Kasim No.48, Kampung Dalam, 
   Kec. Siak, Kabupaten Siak, Riau 28671

Gunakan pengetahuan ini untuk menjawab pertanyaan dengan akurat dan profesional."]]
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